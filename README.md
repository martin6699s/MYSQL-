# 记录下在虚拟机上配置MYSQL主从配置的过程
# 测试主从服务器是否生效，将做个选择性批量替换数据表敏感数据的PHP项目,该项目将采用LNMP方式运行，尝试使用Laravel5.1+Angular.js搭建后台
  首先选用VirtualBox 5.1.14虚拟机安装第一台Ubuntu服务器(x64,版本14.04)，安装过程就不详细说明，要说明的是如何主机如何和虚拟机互相访问：
  VirtualBox有用得比较多的三种网络工作模式 bridged(桥接)，NAT(网络地址转换)，host-only(主机模式) ：                                 
    桥接模式按个人理解就是和宿主主机共享一个网卡，就像一个虚拟的网桥，一端架在主机的网卡上，另一端连接到你的虚拟主机，由该网卡绑定了主机IP和虚拟机主机IP，多IP监听，此模式可以实现局域网内电脑互相访问；
    NAT模式相当于在虚拟机主机前面加一个路由，该路由用于虚拟机和外面的信息互转与传递，虚拟机主机发送信息给NAT路由或者叫服务器，又NAT路由转发出去，此模式可以连接外网，与外网互访。
    host-only模式 顾名思义就是只有宿主主机和虚拟机主机之间互访模式。


#我选用NAT+host-only，那么久需要配置两张虚拟网卡(优点:虚拟机能用host-only与宿主主机连接，用NAT与外网连接，缺点：宿主主机与通NAT下的IP无法连接)
首先设置NAT: 在VirtualBox菜单栏选VirtualBox -> 偏好设置 ->  网络 -> NAT网络 -> 右边有添加NAT网络按钮-> 添加后双击该网络(通常的命名为NatNetwork) -> 确保“启动网络  通常网络为10.0.2.0/24,支持DHCP”->  回到Ubuntu Server 虚拟机 选该虚拟机并点“设置”-> 网络 -> 网卡1选网络地址转换(NAT) -> 进入虚拟机Ubuntu ->  cat /etc/network/interfaces 查看第一块网卡eth0在该文件中，我的interfaces文件上显示iface eth0 inet dhcp -> 完成
设置host-only: 在VirtualBox菜单栏选VirtualBox -> 偏好设置 ->  网络 -> 选仅主机(Host-Only)模式,之后它帮我创建了vboxnet0网络 -> 双击vboxnet0-> 配置主机虚拟网络IPv4地址为192.168.56.1/24 -> 单机DHCP服务器，启动服务器，添服务器地址192.168.56.100/24, 最小最大地址都添192.168.56.101好了，一个就行 -> 回到Ubuntu Server 虚拟机 选该虚拟机并点“设置”-> 网络 -> 网卡2选择host-only连接模式，界面名称选vboxnet0,高级下控制芯片我选Intel PRO/1000 MT -> 进入虚拟机Ubuntu ->  vim /etc/network/interfaces 我的interfaces文件里加入
#VirtualBox Host-only mode
auto eth1
iface eth1 inet static
address 192.168.56.101
netmask 255.255.255.0

加入上述文件后保存，执行service  networking restart 居然显示失败(stop: Job failed while stopping),只能求助谷歌，原来传统的service重启和停止网络不再支持，需要通过ifdown & ifup来实现操作：
#sudo ifdown eth0 && ifup eth0 --重启指定网卡
#sudo ifdown --exclude=lo && sudo ifup --exclude=lo -a 重启楚lo网卡的所有网卡(lo网卡指的是本机环路接口127.0.0.1)
重启完成。
虚拟机ping 8.8.8.8 正常
宿主主机ping 192.168.56.101 正常

#第二台虚拟主机配置：
在virtualBox上克隆第一台虚拟主机 并重置该虚拟机所有网卡，选择全部复制。然后修改第二台虚拟主机的主机名，需要把/etc/hosts和/etc/hostname修改成别的主机名 保存退出重启。
按上面配置网络那样配置第二台虚拟主机，主要是host-only网络的IP不一样，其他全一样。
这样两条虚拟主机就可以互相访问了。


#接下来配置虚拟机里mysql和防火墙 让宿主主机能访问虚拟机上的mysql数据库
打开/etc/mysql/my.cnf
会看到一条记录：bind-address = 127.0.0.1 
该配置说明mysql默认在127.0.0.1下监听请求，要使宿主主机能连接该数据库，必须将此行注释掉或者将IP改为虚拟机相对于主机可访问到的IP,如我配的192.168.56.101(第一台虚拟机)
修改完my.cnf后需要重启mysql ,命令：sudo service mysql restart

#sudo mysql_secure_installation
按步骤按Y 到了一项“Disallow root login remotely?" 选n
这样就可以用root用户远程登录了，不然就必须配置mysql用户了

mysql授权远程登录数据库(参考某博客的：https://my.oschina.net/zzq911013/blog/724036)
(1) 切换数据库 ：use mysql
(2) 输入命令授权：grant all privileges on *.* to 'root'@'%' identified by 'password' with grant option;
(3) 刷新权限 flush privileges;
(4) exit;退出mysql后，查看防火墙是否开启：
   sudo ufw status
(5) 显示"Status: inactive", 说明防火墙已启动，通过以下命令开启3306端口
sudo ufw allow 3306/tcp
到这里要注意不能在去刷udo mysql_secure_installation，不然又得重来(1);
另外sudo ufw delete allow 3306/tcp就是静止端口
查看mysql监听端口：
martin@xxx:~$ netstat -tap | grep mysql
(No info could be read for "-p": geteuid()=1000 but you should be root.)
tcp6       0      0 [::]:mysql              [::]:*                  LISTEN      -
tcp6       0      0 192.168.56.101:mysql    192.168.56.1:56055      ESTABLISHED -
tcp6       0      0 192.168.56.101:mysql    192.168.56.1:56056      ESTABLISHED -





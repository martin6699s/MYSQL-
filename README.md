# 记录下在虚拟机上配置MYSQL主从配置(曲折之路)的过程
## 测试主从服务器是否生效，将做个选择性批量替换数据表敏感数据的PHP项目,该项目将采用LNMP方式运行，尝试使用Laravel5.1+Angular.js搭建后台
  首先选用VirtualBox 5.1.14虚拟机安装第一台Ubuntu服务器(x64,版本14.04)，名字叫 Ubuntu-Server-x64-master,安装过程就不详细说明，
  安装mysql5.6数据库可参考(https://www.digitalocean.com/community/tutorials/how-to-install-mysql-on-ubuntu-14-04 和 http://blog.chinaunix.net/uid-23284114-id-5520029.html ),要说明的是如何主机如何和虚拟机互相访问：
  VirtualBox有用得比较多的三种网络工作模式 bridged(桥接)，NAT(网络地址转换)，host-only(主机模式) ：                                 
    桥接模式按个人理解就是和宿主主机共享一个网卡，就像一个虚拟的网桥，一端架在主机的网卡上，另一端连接到你的虚拟主机，由该网卡绑定了主机IP和虚拟机主机IP，多IP监听，此模式可以实现局域网内电脑互相访问；
    NAT模式相当于在虚拟机主机前面加一个路由，该路由用于虚拟机和外面的信息互转与传递，虚拟机主机发送信息给NAT路由或者叫服务器，又NAT路由转发出去，此模式可以连接外网，与外网互访。
    host-only模式 顾名思义就是只有宿主主机和虚拟机主机之间互访模式。


##我选用NAT+host-only，那么久需要配置两张虚拟网卡(优点:虚拟机能用host-only与宿主主机连接，用NAT与外网连接，缺点：宿主主机与通NAT下的IP无法连接)
首先设置NAT: 在VirtualBox菜单栏选VirtualBox -> 偏好设置 ->  网络 -> NAT网络 -> 右边有添加NAT网络按钮-> 添加后双击该网络(通常的命名为NatNetwork) -> 确保“启动网络  通常网络为10.0.2.0/24,支持DHCP”->  回到Ubuntu Server 虚拟机 选该虚拟机并点“设置”-> 网络 -> 网卡1选网络地址转换(NAT) -> 进入虚拟机Ubuntu ->  cat /etc/network/interfaces 查看第一块网卡eth0在该文件中，我的interfaces文件上显示iface eth0 inet dhcp -> 完成
设置host-only: 在VirtualBox菜单栏选VirtualBox -> 偏好设置 ->  网络 -> 选仅主机(Host-Only)模式,之后它帮我创建了vboxnet0网络 -> 双击vboxnet0-> 配置主机虚拟网络IPv4地址为192.168.56.1/24 -> 单机DHCP服务器，启动服务器，添服务器地址192.168.56.100/24, 最小最大地址都添192.168.56.101好了，一个就行 -> 回到Ubuntu Server 虚拟机 选该虚拟机并点“设置”-> 网络 -> 网卡2选择host-only连接模式，界面名称选vboxnet0,高级下控制芯片我选Intel PRO/1000 MT -> 进入虚拟机Ubuntu ->  vim /etc/network/interfaces 我的interfaces文件里加入
##VirtualBox Host-only mode
auto eth1
iface eth1 inet static
address 192.168.56.101
netmask 255.255.255.0

加入上述文件后保存，执行service  networking restart 居然显示失败(stop: Job failed while stopping),只能求助谷歌，原来传统的service重启和停止网络不再支持，需要通过ifdown & ifup来实现操作：
##sudo ifdown eth0 && ifup eth0 --重启指定网卡
###sudo ifdown --exclude=lo -a && sudo ifup --exclude=lo -a 重启除lo网卡的所有网卡(lo网卡指的是本机环路接口127.0.0.1)
重启完成。
虚拟机ping 8.8.8.8 正常
宿主主机ping 192.168.56.101 正常

##第二台虚拟主机(名字叫Ubuntu-Server-x64-slave)配置：
在virtualBox上克隆第一台虚拟主机 并重置该虚拟机所有网卡，选择全部复制。然后修改第二台虚拟主机的主机名，需要把/etc/hosts和/etc/hostname修改成别的主机名 保存退出重启。
按上面配置网络那样配置第二台虚拟主机，主要是host-only网络的IP不一样，其他全一样。
这样两条虚拟主机就可以互相访问了。


##接下来配置虚拟机里mysql和防火墙 让宿主主机能访问虚拟机上的mysql数据库
打开/etc/mysql/my.cnf
会看到一条记录：bind-address = 127.0.0.1 
该配置说明mysql默认在127.0.0.1下监听请求，要使宿主主机能连接该数据库，必须将此行注释掉或者将IP改为虚拟机相对于主机可访问到的IP,如我配的192.168.56.101(第一台虚拟机)(如绑定0.0.0.0将匹配所有IP主机,表示全网络，关于0.0.0.0知识，看了别人写的挺好：http://liuzhigong.blog.163.com/blog/static/17827237520114207278610/)
修改完my.cnf后需要重启mysql ,命令：sudo service mysql restart

##sudo mysql_secure_installation
按步骤按Y 到了一项“Disallow root login remotely?" 选n
这样就可以用root用户远程登录了，不然就必须配置mysql用户了

mysql授权远程登录数据库(参考某博客的：https://my.oschina.net/zzq911013/blog/724036)
(1) 切换数据库 ：use mysql
(2) 输入命令授权(这里的password时root用户在mysql上的密码，个人理解%表示模糊匹配，匹配所有IP域名)：grant all privileges on *.* to 'root'@'%' identified by 'password' with grant option;
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

或者
sudo lsof -i :3306
查看3306端口被哪个程序占用

####
记录下连接mysql遇到的问题：
(1)、ERROR 2002 (HY000): Can't connect to local MySQL server through socket '/var/run/mysqld/mysqld.sock' (2)
有一次莫名其妙启动虚拟机，mysql -uroot -pxxx 连接本地数据库报出上面错误，看了stackoverflow，可能是配置出错导致，/var/run/mysqld/mysqld.sock的产生在my.cnf里，看了下配置来bind-address时没有该IP地址，才想起来曾经改过虚拟机静态IP;改成虚拟机IP后，
sudo service mysql stop ,sudo service mysql start下又好了。

##NAT+host-only方式，两虚拟机之间能互相ping通，走的时NAT,但mysql死活无法远程连接(后续再弄清楚)，只能采用桥接方式+host-only方式或者内部网络+host-only方式，我选了内部网络+host-only方式。
(1).首先设置虚拟机dhcp,在宿主主机的命令行上 输入：vboxmanage dhcpserver add --netname testlab --ip 10.10.10.1 --netmask 255.255.255.0 --lowerip 10.10.10.2 --upperip 10.10.10.255 --enable
(http://askubuntu.com/questions/623583/how-can-i-setup-an-internal-network-with-virtualbox-ubuntu-14-04)

这样就开启一个dhcp服务器的内部网络(名字叫testlab,这个后面在设置虚拟机网络名(界面名称)时会用到)
(2).选择第一台虚拟机主机,点击设置 --> 选择网络 --> 网卡1，把刚才设置成‘NAT’连接方式成‘内部网络’连接方式，然后界面名称就输入刚才的网络名'testlab',这是第一块网卡eth0配置
(3).选择第二台虚拟主机，重复步骤(2)
(4).同时启动两台虚拟机，因为之前两台虚拟机的第一块网卡都在/etc/network/interfaces文件里面加了
auto eth0 
iface lo inet dhcp
没有的自行加上。
(5).因为两个虚拟机都在10.10.10.x网段上，dhcp自动分给了第一台‘10.10.10.2’ IP地址，第二台为‘10.10.10.3’ IP地址，通过ifconfig可以看到。然后就需要我们在重复上面的设置mysql中的my.cnf和刷权限，防火墙之前开放3306端口，没开的请重新开。最后通过lsof -i :3306 或netstat 看有没有绑定IP和端口。
这里建议在主从mysql数据库上新建一个用户如‘user’及对应的密码如'xxxxxx'，用该用户远程登录虚拟机mysql







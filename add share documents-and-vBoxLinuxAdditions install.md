# 添加共享文件夹

## 首先需要安装virtualBox虚拟机增强工具
1. 在virtualBox中的虚拟机的菜单栏选 Devices(设备) --> insert Guest Additions CD image(安装增强工具)，这是在虚拟机设置-存储-控制器:IDE就有挂了虚拟机增强工具的镜像。
2. 然后打开虚拟机，输入 cd /mnt/cdrom(如果没有该目录 请sudo mkdir /mnt/cdrom),这个目录用于挂载CD介质，现在开始挂载：sudo mount -t iso9660 /dev/cdrom /mnt/cdrom
3. 在进入/mnt/cdrom 看到里面有很多文件，其中VBoxLinuxAdditions.run;
4. 输入sh ./VBoxLinuxAdditions.run,报错误：
martin@xxx:/mnt/cdrom$ sudo sh ./VBoxLinuxAdditions.run
Verifying archive integrity... All good.
Uncompressing VirtualBox 5.1.14 Guest Additions for Linux...........
VirtualBox Guest Additions installer
Copying additional installer modules ...
Installing additional modules ...
vboxadd.sh: Building Guest Additions kernel modules.
vboxadd.sh: You should restart your guest to make sure the new modules are actually used.
vboxadd.sh: Starting the VirtualBox Guest Additions.

Could not find the X.Org or XFree86 Window System, skipping.

因为Ubuntu server没有安装Xwindow，运行安装 `sudo apt-get install xserver-xorg xserver-xorg-core`

5. 再次执行 sudo sh ./VBoxLinuxAdditions.run
martin@xxx:/mnt/cdrom$ sudo sh ./VBoxLinuxAdditions.run
Verifying archive integrity... All good.
Uncompressing VirtualBox 5.1.14 Guest Additions for Linux...........
VirtualBox Guest Additions installer
Removing installed version 5.1.14 of VirtualBox Guest Additions...
vboxadd.sh: Stopping VirtualBox Additions.
You may need to restart your guest system to finish removing the guest drivers.
Copying additional installer modules ...
Installing additional modules ...
vboxadd.sh: Building Guest Additions kernel modules.
vboxadd.sh: You should restart your guest to make sure the new modules are actually used.
vboxadd.sh: Starting the VirtualBox Guest Additions.

You may need to restart the Window System (or just restart the guest system)
to enable the Guest Additions.
安装完成。其他问题引起无法安装增强工具可以参考(内含ubuntu 安装)：[链接](http://limitx5.blogspot.hk/2016/04/openmpi-virtualbox-50-ubuntu-server-x64.html)

6. 关闭虚拟机，在设置-存储-控制器:IDE 删除掉增强工具的iso镜像，点确定。(养成好习惯 把没用的镜像去掉)
7. 在设置-共享文件夹-添加共享文件夹-选择宿主主机里的路径，共享文件夹名称填Documents, 这是你要挂载的文件夹名，选自动挂载，开启虚拟机，
在进入`~`家目录，mkdir Work,然后开始挂载共享文件夹：sudo mount -t vboxsf Documents /home/martin/Work 。这是就能看到/home/martin/Work目录里面有宿主主机Documents共享文件夹的资源了。
说明 ：`sudo mount -t vboxsf share mount_point`
 -t表示指明是哪个文件系统类型，share就是我们刚才在virtualBox设置共享文件夹填的共享文件夹名，mount_point就是在ubuntu操作系统里面挂载的文件夹绝对路径。

8. 上述第7条只是暂时挂载，系统重启后又没了。
需要长久挂载 需要在/etc/fstab文件中加入 `Documents /home/martin/Work vboxsf rw,gid=100,uid=1000,auto 0 0`
然后重启虚拟机(其中uid是用户ID，不知道自己用户ID可以在命令行上敲id回车就可以看到)
重启过程报错如下：
```
An error occurred while mounting /home/martin/Work.  
keys:Press S to skip mounting or M for manual recovery 
```

网上查说挂载共享文件夹是需要vboxsf，但那时候还没到它加载，所以报错。那么就按它提示先跳过再想办法，按S 跳过。

网上说有两种方法：一种是让它提前加载，在root用户下在/etc/modules加入一行vboxsf.但一直没成功过。所以我用了了第二种：
在/etc/rc.local加入一行：
```
mount.vboxsf -w Documents /home/martin/Work
```

再重启虚拟机，这下终于有共享目录了。
参考[链接](http://askubuntu.com/questions/365346/virtualbox-shared-folder-mount-from-fstab-fails-works-once-bootup-is-complete)

9. 有时需要双向拷贝内容，在设置-高级-共享粘贴板选双向，拖放选主机到虚拟机。





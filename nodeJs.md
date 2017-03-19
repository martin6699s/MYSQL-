# 安装nodejs

## 因为之前把NAT撤掉了。现在要访问外网，需要重新启用。启用方式在README.md上有介绍 不多说。现在开始安装nodejs
因为ubuntu用的是国外源，所以需要配置Ubuntu 14.04 国内源，我选了阿里的源。


```
cp /etc/apt/sources.list /etc/apt/sources.list.bak
```
然后把sources.list里面的数据全清空，然后加入下面的源：

```
#alibaba
deb http://mirrors.aliyun.com/ubuntu/ trusty main restricted universe multiverse
deb http://mirrors.aliyun.com/ubuntu/ trusty-security main restricted universe multiverse
deb http://mirrors.aliyun.com/ubuntu/ trusty-updates main restricted universe multiverse
deb http://mirrors.aliyun.com/ubuntu/ trusty-proposed main restricted universe multiverse
deb http://mirrors.aliyun.com/ubuntu/ trusty-backports main restricted universe multiverse
deb-src http://mirrors.aliyun.com/ubuntu/ trusty main restricted universe multiverse
deb-src http://mirrors.aliyun.com/ubuntu/ trusty-security main restricted universe multiverse
deb-src http://mirrors.aliyun.com/ubuntu/ trusty-updates main restricted universe multiverse
deb-src http://mirrors.aliyun.com/ubuntu/ trusty-proposed main restricted universe multiverse
deb-src http://mirrors.aliyun.com/ubuntu/ trusty-backports main restricted universe multiverse
```
```
sudo apt-get update
```
下载速度瞬间飞起来！

因为我的Ubuntu是14.04 LTS 推荐安装Node.js v.4x
```
curl -sL https://deb.nodesource.com/setup_4.x | sudo -E bash -  
sudo apt-get install -y nodejs 
```

安装成功！查看nodejs -v 和 npm -v 
分别是v4.8.0 和 v2.5.11 
安装过程出现一下警告，因为没设置好本地locale.

perl: warning: Setting locale failed.
perl: warning: Please check that your locale settings:
LANGUAGE = (unset),
LC_ALL = (unset),
LC_CTYPE = "zh_CN.UTF-8",
LANG = "zh_CN.UTF-8"
are supported and installed on your system.
perl: warning: Falling back to the standard locale ("C").

```
sudo locale-gen zh_CN zh_CN.UTF-8

sudo dpkg-reconfigure locales
```

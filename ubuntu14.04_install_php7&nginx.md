可参考
[链接1](https://www.tecmint.com/install-php7-for-apache-nginx-on-ubuntu-14-04/) 
[链接2](https://www.digitalocean.com/community/tutorials/how-to-install-linux-nginx-mysql-php-lemp-stack-on-ubuntu-14-04)

1. 安装PHP7
添加PPA。PPA,表示 Personal Package Archives,个人软件包集；
 ```
sudo apt-get install python-software-properties
sudo add-apt-repository ppa:ondrej/php
sudo apt-get update
```

安装php7.0-fpm及一些扩展(说明下，如果使用sudo apt-get install php7.0 默认就会安装带apache扩展及php7.0-cli，php7.0-common等扩展；如安装php7.0-fpm,那就会代替php7.0(带有apache))
```
sudo apt-get install php7.0-fpm php7.0-cli php7.0-curl php7.0-gd php7.0-mysql php7.0-mbstring php7.0-mcrypt
```
正常会输出并自己输入php -v看php版本：
```
Processing triggers for libc-bin (2.19-0ubuntu6.9) ...
Processing triggers for ureadahead (0.100.0-16) ...
Processing triggers for php7.0-fpm (7.0.20-2~ubuntu14.04.1+deb.sury.org+1) ...
php7.0-fpm stop/waiting
php7.0-fpm start/running, process 9873
php7.0-fpm stop/waiting
php7.0-fpm start/running, process 9916
martin@www:~$ php -v
PHP 7.0.20-2~ubuntu14.04.1+deb.sury.org+1 (cli) (built: Jun 14 2017 05:55:23) ( NTS )
Copyright (c) 1997-2017 The PHP Group
Zend Engine v3.0.0, Copyright (c) 1998-2017 Zend Technologies
    with Zend OPcache v7.0.20-2~ubuntu14.04.1+deb.sury.org+1, Copyright (c) 1999-2017, by Zend Technologies
```
2. 配置PHP7

   * 打开php.ini 使用vim打开并用"/"查找cgi.fix_pathinfo选项，去掉注释，并将其值设为0 (ps：修改前记得备份php.ini)
     `sudo vim /etc/php/7.0/fpm/php.ini`
   * 启动php7.0-mcrypt
     `sudo phpenmod mcrypt`
   * 重启php7.0-fpm
     `sudo service php7.0-fpm restart`

3. 安装nginx
   
   * 下载签名密钥：
       ```
        wget http://nginx.org/keys/nginx_signing.key 
        sudo apt-key add nginx_signing.key  
       ```    
   * 添加nginx源到/etc/apt/sources.list
       ```
       deb http://nginx.org/packages/mainline/ubuntu/ trusty nginx
       deb-src http://nginx.org/packages/ubuntu/ trusty nginx
       ```
   * 安装nginx
       ```
       sudo apt-get update
       sudo apt-get install nginx
       ``` 
   * 测试nginx是否安装成功，因为安装在Master主机上，主机IP为192.168.56.101
    在浏览器上输入192.168.56.101回车看是否有welcome to nginx

   * 修改nginx.conf配置（现nginx版本为1.13.1） 
     在/etc/nginx/conf.d目录下cp default.conf master.conf, 然后在这个master.conf上修改：

4. 安装Laravel项目
   * 安装Composer 
     ```
     sudo apt-get install curl
     cd ~
     curl -sS https://getcomposer.org/installer| php
     sudo mv composer.phar /usr/local/bin/composer
     ```      
   * 安装压缩、解压缩程序

     `sudo apt-get install zip unzip`

    * 安装git(然后把github上的laravel项目克隆下来)
    
     `sudo apt-get install git`
    
    * 生成公钥并上传到github上

      ```
      mkdir ~/.ssh
      cd ~/.ssh
      ssh-keygen -t rsa -C "61*****0@qq.com"

      #把生成的公钥放到github上

      ssh-agent bash

      ssh-add ~/.ssh/私钥
      ```
    *  克隆laravel项目

      ```
      cd /var

      sudo mkdir www

      cd www

      sudo chown -R  当前用户名:当前用户组 /var/www/

      git clone git@github.com:martin6699s/MYSQL-Master-And-Slave-Server-Configuration.git master-slave

      ``` 
    * 修改laravel项目的访问权限
      ```
      sudo chown -R :www-data /var/www/master-slave/datainstead
      sudo chmod -R 775 /var/www/master-slave/datainstead/storage  
      ```

    * 导入laravel项目vendor目录
    ```
     cd /var/www/master-slave/datainstead
     composer install
    ```
    运行install后报错如下：

      ```
      Problem 1
        - Installation request for phpunit/phpunit 4.8.35 -> satisfiable by phpunit/phpunit[4.8.35].
        - phpunit/phpunit 4.8.35 requires ext-dom * -> the requested PHP extension dom is missing from your system.

          To enable extensions, verify that they are enabled in your .ini files:
        - /etc/php/7.0/cli/php.ini
        - /etc/php/7.0/cli/conf.d/10-mysqlnd.ini
        - /etc/php/7.0/cli/conf.d/10-opcache.ini
        - /etc/php/7.0/cli/conf.d/10-pdo.ini
        - /etc/php/7.0/cli/conf.d/20-calendar.ini
        - /etc/php/7.0/cli/conf.d/20-ctype.ini
        - /etc/php/7.0/cli/conf.d/20-curl.ini
        - /etc/php/7.0/cli/conf.d/20-exif.ini
        - /etc/php/7.0/cli/conf.d/20-fileinfo.ini
        - /etc/php/7.0/cli/conf.d/20-ftp.ini
        - /etc/php/7.0/cli/conf.d/20-gd.ini
        - /etc/php/7.0/cli/conf.d/20-gettext.ini
        - /etc/php/7.0/cli/conf.d/20-iconv.ini
        - /etc/php/7.0/cli/conf.d/20-json.ini
        - /etc/php/7.0/cli/conf.d/20-mbstring.ini
        - /etc/php/7.0/cli/conf.d/20-mcrypt.ini
        - /etc/php/7.0/cli/conf.d/20-mysqli.ini
        - /etc/php/7.0/cli/conf.d/20-pdo_mysql.ini
        - /etc/php/7.0/cli/conf.d/20-phar.ini
        - /etc/php/7.0/cli/conf.d/20-posix.ini
        - /etc/php/7.0/cli/conf.d/20-readline.ini
        - /etc/php/7.0/cli/conf.d/20-shmop.ini
        - /etc/php/7.0/cli/conf.d/20-sockets.ini
        - /etc/php/7.0/cli/conf.d/20-sysvmsg.ini
        - /etc/php/7.0/cli/conf.d/20-sysvsem.ini
        - /etc/php/7.0/cli/conf.d/20-sysvshm.ini
        - /etc/php/7.0/cli/conf.d/20-tokenizer.ini
      You can also run `php --ini` inside terminal to see which files are used by PHP in CLI mode.

     ```

    原来是缺少php7.0-xml扩展，安装:
    ```
    sudo apt-get install php7.0-xml
    sudo service nginx restart
    然后在composer install
    ```
    * 修改sock访问权限:

      打开/etc/php/7.0/fpm/pool.d/www.conf, 把下面的注释去掉:
      ```
      listen.owner = www-data
      listen.group = www-data
      listen.mode = 0660
      ```
      sudo service php7.0-fpm restart

      修改nginx.conf中user为 www-data www-data

      重启nginx

     * 开启调试模式及生成key
       打开项目目录下config/app.php修改：'debug' => env('APP_DEBUG', true),原本为'debug' => env('APP_DEBUG', false) 

       在终端使用命令:
       php artisan key:generate
       将生成的key复制到config/app.php替换的APP_KEY键值。

       把/var/www/master-slave/datainstead目录下， 把复制.env.example 粘贴重命名成.env 
       cp .env.example .env

       然后把app key配置放在.env 
      
      * 浏览器访问192.168.56.101, 访问是否成功。

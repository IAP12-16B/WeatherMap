#!/usr/bin/env bash

yum -y upgrade
yum -y install nano gd gd-devel php53u-gd

# Enable keep alive in httpd.conf
sed 's|KeepAlive Off|KeepAlive On|' </etc/httpd/conf/httpd.conf >/etc/httpd/conf/httpd_mod.conf
# enable .htaccess to overwrite .conf rules
# sed 's|AllowOverride None|KeepAlive All|' </etc/httpd/conf/httpd.conf >/etc/httpd/conf/httpd_mod.conf # already set
# append index.php to DirectoryIndex
sed 's|DirectoryIndex index.html|DirectoryIndex index.php index.html|' </etc/httpd/conf/httpd.conf >/etc/httpd/conf/httpd_mod.conf

mv /etc/httpd/conf/httpd_mod.conf /etc/httpd/conf/httpd.conf


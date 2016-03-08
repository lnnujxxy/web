#!/bin/bash

function init()
{
	LANG=C
	yum -y install iotop screen python-pip wget dos2unix gcc gcc-c++ autoconf libjpeg libjpeg-devel libpng libpng-devel freetype freetype-devel libxml2 libxml2-devel zlib zlib-devel glibc glibc-devel glib2 glib2-devel bzip2 bzip2-devel ncurses ncurses-devel curl curl-devel openssl openssl-devel openldap openldap-devel libmcrypt libmcrypt-devel lua-devel nc rsyslog
}

function create_project()
{
	mkdir -p /opt/htdocs
	mkdir -p /opt/web
	mkdir -p /opt/data/log
	chmod -R 0777 /opt/data
	chown -R www:www /opt
}

function add_user_group()
{
	groupadd www && useradd www -g www
	echo "rai2Ber0" | passwd www --stdin > /dev/null 2>&1
	groupadd develop && useradd biaobai -g develop
	echo "biaobaiapp123" | passwd biaobai --stdin > /dev/null 2>&1
	sed -i 's/PermitRootLogin yes/PermitRootLogin no/' /etc/ssh/sshd_config
	service sshd restart
	echo -e "user and group create"

	#建立发布机到新主机的信任
	#在发布机以为www身份，运行#ssh 新主机ip 'pwd'
}

init
#add_user_group
create_project

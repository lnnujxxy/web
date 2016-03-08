#!/bin/bash

openresty_dir="/usr/local/openresty"
php_dir="/usr/local/php"
soft_dir="/tmp/soft"
download_url="http://soft.dev.shiyuehehu.com"
project_dir="/opt/htdocs"

if [ -n "$1" ]; then
env=$1
else
env="product"
fi

function download()
{
	mkdir -p ${soft_dir}
	cd ${soft_dir}
	echo -e "starting download soft"
	cat > list << "EOF" &&
pcre-8.10.zip
ngx_openresty-1.9.3.1.tar.gz
openresty
php-5.6.13.tar.gz
php-fpm
yaf-2.3.3.tgz
msgpack-0.5.5.tgz
redis-server
redis-2.8.12.tar.gz
phpredis.tar.gz
memcache-2.2.7.tgz
libevent-1.4.14b-stable.tar.gz
phpunit.phar
supervisord
GraphicsMagick-1.3.21.tar.gz
gmagick-1.1.7RC3.tgz
EOF

	for i in `cat list`
	do
		if [ -s $i ]; then
			echo -e "$i found"
		else
			echo -e "$i not found, download now..."
			wget ${download_url}/$i
		fi
	done
}

function install_openresty()
{
	cd ${soft_dir}
	rm -rf pcre-8.10
	unzip pcre-8.10.zip
	cd pcre-8.10

	./configure
	make && make install
	
	cd ${soft_dir}
	rm -rf ngx_openresty-1.9.3.1
	tar zxvf ngx_openresty-1.9.3.1.tar.gz
	cd ngx_openresty-1.9.3.1
	./configure --prefix=${openresty_dir} --user=www --group=www

	make && make install

	cp -rf ${soft_dir}/openresty /etc/init.d/openresty
	chmod +x /etc/init.d/openresty

	/etc/init.d/openresty stop
	/etc/init.d/openresty start
	/etc/init.d/openresty reload
	
	if [ `ps -ef|grep openresty|wc -l` -gt 1 ]; then
		echo -e "install openresty sucussfully!"	
	else	
		echo -e "install openresty failure!"
		exit;
	fi
}

function install_php()
{
	cd ${soft_dir}
	tar zxvf php-5.6.13.tar.gz	
	cd php-5.6.13
	./configure --prefix=/usr/local/php --enable-fpm --enable-mbstring --enable-pdo --with-curl --disable-debug --disable-rpath --enable-inline-optimization --with-bz2 --with-zlib --enable-sockets --enable-sysvsem --enable-sysvshm --enable-pcntl --enable-mbregex --with-mhash --enable-zip --with-pcre-regex --with-mysql --with-mysqli --with-gd --with-jpeg-dir --with-config-file-path=${php_dir}/etc --disable-fileinfo 
	make && make install
	cd ..

	mv ${php_dir}/etc/php-fpm.conf.default ${php_dir}/etc/php-fpm.conf
	#cp -rf ${soft_dir}/php-5.6.13/php.ini-development ${php_dir}/etc/php.ini

	echo "export PATH=$PATH:${php_dir}/bin" >> /etc/profile
	source /etc/profile
	cp -rf ${soft_dir}/php-fpm /etc/init.d/php-fpm
	chmod +x /etc/init.d/php-fpm
	/etc/init.d/php-fpm stop
	/etc/init.d/php-fpm start

	if [ `ps -ef|grep php-fpm|wc -l` -gt 1 ]; then
		echo -e "install php sucussfully!"	
	else	
		echo -e "install php failure!"
		exit;
	fi
}

function ln_php_conf() 
{
	rm -rf ${php_dir}/etc/php.ini
	rm -rf ${php_dir}/etc/php-fpm.conf
	ln -s ${project_dir}/conf/${env}/php.ini ${php_dir}/etc/php.ini
	ln -s ${project_dir}/conf/${env}/php-fpm.conf ${php_dir}/etc/php-fpm.conf

	/etc/init.d/php-fpm stop
	/etc/init.d/php-fpm start
}

function install_yaf()
{
	cd ${soft_dir}	
	tar zxvf yaf-2.3.3.tgz	
	cd yaf-2.3.3
	${php_dir}/bin/phpize
	./configure --with-php-config=${php_dir}/bin/php-config	
	make && make install
}

function install_msgpack()
{
	cd ${soft_dir}
	tar zxvf msgpack-0.5.5.tgz
	cd msgpack-0.5.5
	${php_dir}/bin/phpize
	./configure --with-php-config=${php_dir}/bin/php-config
	make && make install
}

function install_pdo_mysql()
{
	cd ${soft_dir}
	cd php-5.6.13/ext/pdo_mysql
	${php_dir}/bin/phpize
	./configure --with-php-config=${php_dir}/bin/php-config
	make && make install
}

function install_openssl()
{
	cd ${soft_dir}
	cd php-5.6.13/ext/openssl
	cp config0.m4 config.m4
	${php_dir}/bin/phpize
	./configure --with-php-config=${php_dir}/bin/php-config
	make && make install
}

function install_mcrypt()
{
	cd ${soft_dir}
	cd php-5.6.13/ext/mcrypt
	${php_dir}/bin/phpize
	./configure --with-php-config=${php_dir}/bin/php-config
	make && make install
}

function install_bcmath()
{
	cd ${soft_dir}
	cd php-5.6.13/ext/bcmath
	${php_dir}/bin/phpize
	./configure --with-php-config=${php_dir}/bin/php-config
	make && make install

}

function install_phpredis() 
{
	cd ${soft_dir}
	tar zxvf phpredis.tar.gz
	cd phpredis
	${php_dir}/bin/phpize
	./configure --with-php-config=${php_dir}/bin/php-config
	make && make install
}

function install_memcache()
{
	cd ${soft_dir}
	tar zxvf memcache-2.2.7.tgz
	cd memcache-2.2.7
	${php_dir}/bin/phpize
	./configure --with-php-config=${php_dir}/bin/php-config
	make && make install
}

function install_memcacheq()
{
	cd ${soft_dir}
	tar zxvf db-5.0.21.tar.gz
	cd db-5.0.21/
	cd buid_unix/
	../dist/configure
	make && make install
	
	cd ${soft_dir}
	tar zxvf libevent-1.4.14b-stable.tar.gz
	cd libevent-1.4.14b-stable
	./configure
	make && make install

	echo "/usr/local/lib" >> /etc/ld.so.conf
	echo "/usr/local/BerkeleyDB.5.0/lib" >> /etc/ld.so.conf
	ldconfig

	cd ${soft_dir}
	tar zxvf memcacheq-0.2.0.tar.gz
	cd memcacheq-0.2.0
	./configure --enable-threads --with-bdb=/usr/local/BerkeleyDB.5.0
	make && make install

	mkdir -p /opt/memcacheq
	mkdir -p /opt/data
	#这里最好绑定使用内网ip
	memcacheq -u root -d -r -H /opt/memcacheq -N -R -L 1024 -B 1024 > /opt/data/log/mq-error.log 2>&1 &
	
	if [ `ps -ef|grep memcacheq|wc -l` -gt 0 ]; then
		echo -e "install memcacheq successfully!"
	else	
		echo -e "install memcacheq failure!"
		exit
	fi
}

function install_redis() 
{
	mkdir -p /data/redis
	cd ${soft_dir}
	tar zxvf redis-2.8.12.tar.gz
	cd redis-2.8.12
	make
	make install
	echo vm.overcommit_memory=1 >> /etc/sysctl.conf
	sysctl vm.overcommit_memory=1
	ln -s ${project_dir}/conf/${env}/redis.conf /etc/redis.conf

	cp ${soft_dir}/redis /etc/init.d/redis
	chmod +x /etc/init.d/redis
	if [ `ps -ef|grep redis-server|wc -l` -gt 0 ]; then
		echo -e "install redis successfully!"
	else	
		echo -e "install redis failure!"
		exit
	fi
	
}

function install_phpunit()
{
	cd ${soft_dir}
	chmod +x phpunit.phar
	mv phpunit.phar /usr/local/bin/phpunit
}

function install_composer()
{
	curl -sS https://getcomposer.org/installer | php
	sudo mv composer.phar /usr/local/bin/composer
}

function install_supervisor()
{
	cd ${soft_dir}
	wget -q http://peak.telecommunity.com/dist/ez_setup.py
	python ez_setup.py
	easy_install supervisor
	rm -rf /etc/supervisord.conf
	mkdir -p /var/log/supervisor
	chmod -R 0777 /var/log/supervisor

	ln -s ${project_dir}/conf/${env}/supervisor/supervisord.conf /etc/supervisord.conf
	cp supervisord /etc/init.d/supervisord	
	chmod +x /etc/init.d/supervisord

	/etc/init.d/supervisord stop
	/etc/init.d/supervisord start
}

function start_rsyslog()
{
	ln -nsf ${project_dir}/conf/rsyslog/nginx.conf /etc/rsyslog.d/nginx.conf
	ln -nsf ${project_dir}/conf/rsyslog/php.conf /etc/rsyslog.d/php.conf
	/etc/init.d/rsyslog restart
}

function logrotate()
{
	ln -nsf ${project_dir}/conf/logrotate/biaobai /etc/logrotate.d/biaobai
}

function install_gmagick()
{
	cd ${soft_dir}
	tar zxvf GraphicsMagick-1.3.21.tar.gz
	cd GraphicsMagick-1.3.21
	./configure --without-prel --enable-shared --disable-openmp
	make && make install

	cd ${soft_dir}
	tar zxvf gmagick-1.1.7RC3.tgz
	cd gmagick-1.1.7RC3
	./configure --with-php-config=/usr/local/php/bin/php-config
	make && make install

	echo "extension=gmagick.so" >> /usr/local/php/etc/php.ini
	/etc/init.d/php-fpm restart
}

download
install_supervisor
install_openresty

install_php
install_yaf
install_msgpack
install_pdo_mysql
install_openssl
install_mcrypt
install_bcmath
install_phpredis
install_memcache

ln_php_conf

install_phpunit


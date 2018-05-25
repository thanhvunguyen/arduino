## Main web server setup

MySQL5.6 - 5.7
http://blog-en.mamp.info/2015/07/how-to-use-mysql-5-6-with-mamp-and-mamp.html

Ubuntu setup
php5-json php5-mcrypt php5-intl php5-imagick 

PHP7-
After removing all php* source.

sudo yum install php70 php7-pear php70-cli php70-common php70-intl php70-json php70-mbstring php70-mcrypt php70-mysqlnd php70-pdo php70-pecl-apcu php70-pecl-imagick php70-process php70-xml php70-gd php70-opcache

* Installing pear
* ImageMagick install from yum @amzn-update

##Batch server

###Installing supervisor

* http://supervisord.org/installing.html
* https://nicksergeant.com/running-supervisor-on-os-x/
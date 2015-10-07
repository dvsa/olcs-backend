#!/bin/sh

DBNAME=$(php -r "\$config=require('config/autoload/local.php'); echo \$config['doctrine']['connection']['orm_default']['params']['dbname'];")
DBUSER=$(php -r "\$config=require('config/autoload/local.php'); echo \$config['doctrine']['connection']['orm_default']['params']['user'];")
DBPASSWORD=$(php -r "\$config=require('config/autoload/local.php'); echo \$config['doctrine']['connection']['orm_default']['params']['password'];")

mysqldump -u$DBUSER -p$DBPASSWORD $DBNAME --complete-insert --no-create-info > data.sql

tar -czf ../release/olcs-backend/$VERSION.tar.gz \
composer.phar composer.json composer.lock init_autoloader.php \
config module public data/autoload data/cache vendor \
data.sql olcs-etl/olcs_schema.sql \
--exclude="config/autoload/local.php" --exclude="config/autoload/local.php.dist"

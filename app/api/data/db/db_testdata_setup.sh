#!/bin/sh

mysql -uroot -ppassword < DatabaseSetup.sql

../../vendor/bin/doctrine-module orm:schema:update --force

mysql -uroot -ppassword olcs_be < Rollout.sql

mysql -uroot -ppassword olcs_be < ../../../olcs-etl/olcs-refdata.sql

mysql -uroot -ppassword olcs_be < testdata.sql

sudo service httpd restart

echo "All done!"

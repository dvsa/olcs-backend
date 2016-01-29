#!/bin/sh

mysql -uroot -ppassword olcs_be < Rollout.sql

mysql -uroot -ppassword olcs_be < ../../../olcs-etl/olcs-refdata.sql

mysql -uroot -ppassword olcs_be < UserTesting/minimum_data.sql

sudo service httpd restart

echo "All done!"

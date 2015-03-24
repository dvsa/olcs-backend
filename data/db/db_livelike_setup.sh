#!/bin/sh

#mysql -uroot -ppassword < DatabaseSetup.sql
#../../vendor/bin/doctrine-module orm:schema:update --force

#mysql -uroot -ppassword olcs_be < Rollout.sql
cat Etl/before.sql Etl/schema-$1*sql Etl/after.sql | mysql -uroot -ppassword olcs_be

#sudo service httpd restart

echo "All done!"

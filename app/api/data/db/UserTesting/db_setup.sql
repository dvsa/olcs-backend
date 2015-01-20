#!/bin/sh

mysql -uroot -ppassword < DatabaseSetup.sql

../../../vendor/bin/doctrine-module orm:schema:update --force

mysql -uroot -ppassword olcs_user_testing < ../Rollout.sql

mysql -uroot -ppassword olcs_user_testing < minimum_data.sql

echo "All done!"

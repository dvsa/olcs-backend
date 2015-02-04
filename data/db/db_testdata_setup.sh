#!/bin/sh

mysql -uroot -ppassword < DatabaseSetup.sql

../../vendor/bin/doctrine-module orm:schema:update --force

#mysql -uroot -ppassword olcs_be < schema.sql

mysql -uroot -ppassword olcs_be < Rollout.sql

mysql -uroot -ppassword olcs_be < testdata.sql

echo "All done!"

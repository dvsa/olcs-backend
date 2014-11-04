#!/bin/sh

mysql -uroot -ppassword < DatabaseSetup.sql

../../vendor/bin/doctrine-module orm:schema:update --force

mysql -uroot -ppassword olcs_be < Rollout.sql

echo "All done!"

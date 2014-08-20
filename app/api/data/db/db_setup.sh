#!/bin/sh

mysql -uroot -ppassword < DatabaseSetup.sql

pushd ../../../olcs-backend/vendor/bin/

./doctrine-module orm:schema:update --force

popd

mysql -uroot -ppassword olcs_be < Rollout.sql

mysql -uroot -ppassword olcs_be < DemoData.sql

echo "All done!"

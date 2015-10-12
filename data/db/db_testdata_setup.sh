#!/bin/sh

# drop/create db
mysql -uroot -ppassword < DatabaseSetup.sql

# schema
mysql -uroot -ppassword olcs_be < ../../../olcs-etl/olcs_schema.sql

# rollout data
mysql -uroot -ppassword olcs_be < Rollout.sql

# refdata
echo "-- this file is auto-generated - DO NOT EDIT!" > test-olcs-refdata.sql
grep -v "^CALL\|^USE" ../../../olcs-etl/script_generator/src/main/resources/vosa/olcs/etl/scripts/ref_data.sql >> test-olcs-refdata.sql
mysql -uroot -ppassword olcs_be < test-olcs-refdata.sql
mysql -uroot -ppassword olcs_be < ../../../olcs-etl/olcs_conv_category_refdata.sql

# test data
mysql -uroot -ppassword olcs_be < testdata.sql

sudo service httpd restart

echo "All done!"

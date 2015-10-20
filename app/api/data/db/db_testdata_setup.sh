#!/bin/sh

# drop/create db
echo "DatabaseSetup.sql"
mysql -uroot -ppassword < DatabaseSetup.sql

# schema
echo "olcs_schema.sql"
mysql -uroot -ppassword olcs_be < ../../../olcs-etl/olcs_schema.sql

# rollout data
echo "olcs_rollout_data.sql"
mysql -uroot -ppassword olcs_be < ../../../olcs-etl/olcs_rollout_data.sql

# refdata
echo "-- this file is auto-generated - DO NOT EDIT!" > test-olcs-refdata.sql
grep -v "^CALL\|^USE" ../../../olcs-etl/script_generator/src/main/resources/vosa/olcs/etl/scripts/ref_data.sql >> test-olcs-refdata.sql
echo test-olcs-refdata.sql
mysql -uroot -ppassword olcs_be < test-olcs-refdata.sql
echo olcs_conv_category_refdata.sql
mysql -uroot -ppassword olcs_be < ../../../olcs-etl/olcs_conv_category_refdata.sql

# test data
echo testdata.sql
mysql -uroot -ppassword olcs_be < testdata.sql

sudo service httpd restart

echo "All done!"

#!/bin/sh

# drop/create db
echo "DatabaseSetup.sql"
mysql -uroot -ppassword < DatabaseSetup.sql

# schema
echo "olcs_schema.sql"
mysql -uroot -ppassword olcs_be < ../../../olcs-etl/olcs_schema.sql

# refdata
echo "ref_data.sql"
mysql -uroot -ppassword olcs_be < ../../../olcs-etl/script_generator/src/main/resources/vosa/olcs/etl/scripts/ref_data.sql
echo "olcs_conv_category_refdata.sql"
mysql -uroot -ppassword olcs_be < ../../../olcs-etl/olcs_conv_category_refdata.sql
echo "other_ref_data.sql"
mysql -uroot -ppassword olcs_be < ../../../olcs-etl/script_generator/src/main/resources/vosa/olcs/etl/scripts/other_ref_data.sql

# stub data
echo "olcs_stub_data.sql"
mysql -uroot -ppassword olcs_be < ../../../olcs-etl/olcs_stub_data.sql

# test data
echo "testdata.sql"
mysql -uroot -ppassword olcs_be < testdata.sql

sudo service httpd restart

echo "All done!"

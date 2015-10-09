#!/bin/sh

mysql -uroot -ppassword < DatabaseSetup.sql

mysql -uroot -ppassword olcs_be < ../../../olcs-etl/olcs_schema.sql

mysql -uroot -ppassword olcs_be < Rollout.sql

mysql -uroot -ppassword olcs_be < ../../../olcs-etl/olcs-refdata.sql
#grep -v "^CALL\|USE" ../../../olcs-etl/script_generator/src/main/resources/vosa/olcs/etl/scripts/ref_data.sql > test-olcs-refdata.sql
#mysql -uroot -ppassword olcs_be < test-olcs-refdata.sql

mysql -uroot -ppassword olcs_be < testdata.sql

sudo service httpd restart

echo "All done!"

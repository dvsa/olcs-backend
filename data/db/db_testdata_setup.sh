#!/bin/sh

# causes the shell to exit if any subcommand or pipeline returns a non-zero status.
set -e

# drop/create db
echo "DatabaseSetup.sql"
mysql -uroot -ppassword < DatabaseSetup.sql

# schema
echo "olcs_schema.sql"
mysql -uroot -ppassword olcs_be < ../../../olcs-etl/olcs_schema.sql

# schema
# Add manual Views and Stored Procs
for sqlFile in ../../../olcs-etl/views_procs/*.sql
do
  echo "Executing $sqlFile"
  mysql $connection -e "use olcs_be;\. $sqlFile"
done

# refdata
echo "ref_data.sql"
mysql -uroot -ppassword olcs_be < ../../../olcs-etl/script_generator/src/main/resources/vosa/olcs/etl/scripts/ref_data.sql
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

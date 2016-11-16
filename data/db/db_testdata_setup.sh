#!/bin/sh

# causes the shell to exit if any subcommand or pipeline returns a non-zero status.
set -e

# Check can connect to DB
if ! mysql -e "exit"
then
   echo
   echo "ERROR Cannot connect to database. Make sure you have a ~/.my.cnf file"
   echo
   exit
fi

# drop/create db
echo "Create olcs_be database"
mysql < DatabaseSetup.sql

# schema
echo "Apply OLCS schema"
mysql olcs_be < ../../../olcs-etl/olcs_schema.sql

# schema
# Add manual Views and Stored Procs
echo "View, procs and functions"
for sqlFile in ../../../olcs-etl/views_procs/*.sql
do
  mysql olcs_be < $sqlFile
done

# refdata
echo "ref_data.sql"
mysql olcs_be < ../../../olcs-etl/script_generator/src/main/resources/vosa/olcs/etl/scripts/ref_data.sql
echo "other_ref_data.sql"
mysql olcs_be < ../../../olcs-etl/script_generator/src/main/resources/vosa/olcs/etl/scripts/other_ref_data.sql

# stub data
echo "olcs_stub_data.sql"
mysql olcs_be < ../../../olcs-etl/olcs_stub_data.sql

# test data
echo "testdata.sql"
mysql olcs_be < testdata.sql

# ETL schema is required by pacthes
echo "Create ETL schema (used for logging in patches)"
mysql olcs_be < ../../../olcs-etl/etl-schema.sql

# Apply post-live patches
echo "Apply post-live patches"
cd ../../../olcs-etl
./post-live-patch.sh -d olcs_be -v 4.0.6
./post-live-patch.sh -d olcs_be -v 4.0.7
./post-live-patch.sh -d olcs_be -v 4.0.8
./post-live-patch.sh -d olcs_be -v post-live

sudo service httpd restart

echo "All done!"

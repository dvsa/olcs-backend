#!/bin/bash

read -s -p "Please enter mysql root password: " mypassword

echo "Refreshing tmp database";
mysql -uroot --password=$mypassword -e "DROP DATABASE IF EXISTS olcs_tmp; CREATE DATABASE olcs_tmp";

echo "Importing schema";
mysql -uroot --password=$mypassword olcs_tmp < schema.sql;

echo "Dumping schema_dump";
mysqldump -uroot --password=$mypassword olcs_tmp > schema_dump.sql;

echo "Refreshing tmp database";
mysql -uroot --password=$mypassword -e "DROP DATABASE IF EXISTS olcs_tmp; CREATE DATABASE olcs_tmp";

echo "Importing paul_schema";
mysql -uroot --password=$mypassword olcs_tmp < paul_schema.sql;

echo "Dumping paul_schema";
mysqldump -uroot --password=$mypassword olcs_tmp > paul_dump.sql;

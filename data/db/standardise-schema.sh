#!/bin/bash

read -s -p "Please enter mysql root password: " mypassword

echo "Refreshing tmp database";
mysql -uroot --password=$mypassword -e "DROP DATABASE IF EXISTS olcs_tmp; CREATE DATABASE olcs_tmp";

echo "Importing olcs_schema";
mysql -uroot --password=$mypassword olcs_tmp < olcs_schema.sql;

echo "Dumping olcs_schema";
mysqldump -uroot --password=$mypassword olcs_tmp > olcs_dump.sql;

echo "Refreshing tmp database";
mysql -uroot --password=$mypassword -e "DROP DATABASE IF EXISTS olcs_tmp; CREATE DATABASE olcs_tmp";

echo "Importing paul_schema";
mysql -uroot --password=$mypassword olcs_tmp < paul_schema.sql;

echo "Dumping paul_schema";
mysqldump -uroot --password=$mypassword olcs_tmp > paul_dump.sql;

#!/bin/sh

for sql_file in `ls DataSets/$1/*.sql`;
do
    mysql -uroot -ppassword olcs_be < $sql_file;
done

echo "All done!"

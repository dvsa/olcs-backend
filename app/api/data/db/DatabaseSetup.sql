CREATE DATABASE olcs_be;

CREATE USER olcs_be@localhost IDENTIFIED BY 'password';

GRANT ALL ON *.* TO olcs_be@localhost;
DROP DATABASE IF EXISTS olcs_be;

CREATE DATABASE olcs_be CHARACTER SET utf8 COLLATE utf8_unicode_ci;

GRANT ALL ON olcs_be.* TO olcs_be@localhost IDENTIFIED BY 'password';
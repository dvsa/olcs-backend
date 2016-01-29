DROP DATABASE IF EXISTS olcs_user_testing;

CREATE DATABASE olcs_user_testing;

GRANT ALL ON olcs_user_testing.* TO olcs_user_testing@localhost IDENTIFIED BY 'password';
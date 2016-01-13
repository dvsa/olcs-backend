<?php
error_reporting(E_ALL | E_STRICT);
date_default_timezone_set('Europe/London');
chdir(dirname(__DIR__));

require('Bootstrap.php');

OlcsTest\Bootstrap::init();

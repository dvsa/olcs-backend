<?php

chdir(__DIR__ . '/../');
$loader = require_once(__DIR__ . '/../vendor/autoload.php');
$loader->addPsr4('Cli\\', __DIR__ . '/Cli/');

$cli = new \Cli\AlignEntitiesToSchema();
$cli->run(include('config/application.config.php'));

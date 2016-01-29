<?php

chdir(__DIR__ . '/../');
require_once(__DIR__ . '/../init_autoloader.php');
$loader->addPsr4('Cli\\', __DIR__ . '/Cli/');

$cli = new \Cli\AlignEntitiesToSchema();
$cli->run(include('config/application.config.php'));

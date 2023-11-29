<?php

namespace Dvsa\Olcs\Api\Service\Publication\Process;

use Laminas\ServiceManager\AbstractPluginManager;

class PluginManager extends AbstractPluginManager
{
    protected $instanceOf = ProcessInterface::class;
}

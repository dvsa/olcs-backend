<?php

namespace Dvsa\Olcs\Api\Service\Publication\Process;

use Dvsa\Olcs\Utils\Traits\PluginManagerTrait;
use Laminas\ServiceManager\AbstractPluginManager;

/**
 * Class PluginManager
 * @package Dvsa\Olcs\Api\Service\Publication\Process
 */
class PluginManager extends AbstractPluginManager
{
    use PluginManagerTrait;

    protected $instanceOf = ProcessInterface::class;
}

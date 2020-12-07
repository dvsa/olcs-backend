<?php

namespace Dvsa\Olcs\Api\Domain;

use Laminas\ServiceManager\AbstractPluginManager;
use Laminas\ServiceManager\ConfigInterface;

/**
 * Db Query Service Manager
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 *
 * @method Repository\Query\AbstractRawQuery get($name) Get the Query service
 */
class DbQueryServiceManager extends AbstractPluginManager
{
    public function __construct(ConfigInterface $config = null)
    {
        if ($config) {
            $config->configureServiceManager($this);
        }
    }

    public function validatePlugin($plugin)
    {
    }
}

<?php

/**
 * Repository Service Manager
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain;

use Laminas\ServiceManager\AbstractPluginManager;
use Laminas\ServiceManager\ConfigInterface;

/**
 * Repository Service Manager
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class RepositoryServiceManager extends AbstractPluginManager
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

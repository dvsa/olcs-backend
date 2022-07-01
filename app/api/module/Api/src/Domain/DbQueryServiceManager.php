<?php

namespace Dvsa\Olcs\Api\Domain;

use Dvsa\Olcs\Utils\Traits\PluginManagerTrait;
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
    use PluginManagerTrait;

    protected $instanceOf = null;

    public function __construct(ConfigInterface $config = null)
    {
        if ($config) {
            $config->configureServiceManager($this);
        }
    }
}

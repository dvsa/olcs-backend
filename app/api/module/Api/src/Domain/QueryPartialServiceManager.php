<?php

/**
 * QueryPartial Service Manager
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain;

use Dvsa\Olcs\Utils\Traits\PluginManagerTrait;
use Laminas\ServiceManager\AbstractPluginManager;
use Laminas\ServiceManager\ConfigInterface;

/**
 * QueryPartial Service Manager
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class QueryPartialServiceManager extends AbstractPluginManager
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

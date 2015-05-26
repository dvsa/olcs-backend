<?php

/**
 * QueryPartial Service Manager
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain;

use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\ConfigInterface;

/**
 * QueryPartial Service Manager
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class QueryPartialServiceManager extends AbstractPluginManager
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

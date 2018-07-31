<?php

/**
 * Validation Handler Manager
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain;

use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\ConfigInterface;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\HandlerInterface;
use Zend\ServiceManager\Exception\InvalidServiceException;

/**
 * Validation Handler Manager
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ValidationHandlerManager extends AbstractPluginManager
{
    public function __construct(ConfigInterface $config = null)
    {
        $this->configure(['shareByDefault' => false]);

        if ($config) {
            $config->configureServiceManager($this);
        }
    }

    public function validate($plugin)
    {
        if (!($plugin instanceof HandlerInterface)) {
            throw new InvalidServiceException('Validation handler does not implement HandlerInterface');
        }
    }
}

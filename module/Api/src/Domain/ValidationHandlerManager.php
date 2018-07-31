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
use Zend\ServiceManager\Exception\RuntimeException;

/**
 * Validation Handler Manager
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ValidationHandlerManager extends AbstractPluginManager
{
    public function __construct(ConfigInterface $config = null)
    {
        $this->setShareByDefault(false);

        if ($config) {
            $config->configureServiceManager($this);
        }
    }

    public function validate($plugin)
    {
        if (!($plugin instanceof HandlerInterface)) {
            throw new RuntimeException('Validation handler does not implement HandlerInterface');
        }
    }
}

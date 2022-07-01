<?php

/**
 * Validation Handler Manager
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain;

use Dvsa\Olcs\Utils\Traits\PluginManagerTrait;
use Laminas\ServiceManager\AbstractPluginManager;
use Laminas\ServiceManager\ConfigInterface;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\HandlerInterface;

/**
 * Validation Handler Manager
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ValidationHandlerManager extends AbstractPluginManager
{
    use PluginManagerTrait;

    protected $instanceOf = HandlerInterface::class;

    public function __construct(ConfigInterface $config = null)
    {
        $this->setShareByDefault(false);

        if ($config) {
            $config->configureServiceManager($this);
        }
    }
}

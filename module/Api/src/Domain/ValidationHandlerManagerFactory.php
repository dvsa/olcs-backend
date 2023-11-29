<?php

namespace Dvsa\Olcs\Api\Domain;

use Dvsa\Olcs\Api\Service\AbstractServiceManagerFactory;

class ValidationHandlerManagerFactory extends AbstractServiceManagerFactory
{
    const CONFIG_KEY = 'validation_handlers';
    public const PLUGIN_MANAGER_CLASS = ValidationHandlerManager::class;
}

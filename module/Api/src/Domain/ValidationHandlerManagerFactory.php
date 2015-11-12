<?php

/**
 * Validation Handler Manager Factory
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain;

use Dvsa\Olcs\Api\Service\AbstractServiceManagerFactory;

/**
 * Validation Handler Manager Factory
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ValidationHandlerManagerFactory extends AbstractServiceManagerFactory
{
    const CONFIG_KEY = 'validation_handlers';

    protected $serviceManagerClass = \Dvsa\Olcs\Api\Domain\ValidationHandlerManager::class;
}

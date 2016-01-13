<?php

/**
 * Validator Manager Factory
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain;

use Dvsa\Olcs\Api\Service\AbstractServiceManagerFactory;

/**
 * Validator Manager Factory
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ValidatorManagerFactory extends AbstractServiceManagerFactory
{
    const CONFIG_KEY = 'validator_services';

    protected $serviceManagerClass = \Dvsa\Olcs\Api\Domain\ValidatorManager::class;
}

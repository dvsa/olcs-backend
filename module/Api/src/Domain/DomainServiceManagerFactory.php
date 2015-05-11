<?php

/**
 * Domain Service Manager Factory
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain;

use Dvsa\Olcs\Api\Service\AbstractServiceManagerFactory;

/**
 * Domain Service Manager Factory
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class DomainServiceManagerFactory extends AbstractServiceManagerFactory
{
    const CONFIG_KEY = 'domain_services';

    protected $serviceManagerClass = \Dvsa\Olcs\Api\Domain\DomainServiceManager::class;
}

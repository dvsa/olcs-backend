<?php

/**
 * Repository Service Manager Factory
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain;

use Dvsa\Olcs\Api\Service\AbstractServiceManagerFactory;

/**
 * Repository Service Manager Factory
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class RepositoryServiceManagerFactory extends AbstractServiceManagerFactory
{
    const CONFIG_KEY = 'repository_services';

    protected $serviceManagerClass = \Dvsa\Olcs\Api\Domain\RepositoryServiceManager::class;
}

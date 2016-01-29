<?php

/**
 * Db Query Service Manager Factory
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain;

use Dvsa\Olcs\Api\Service\AbstractServiceManagerFactory;

/**
 * Db Query Service Manager Factory
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class DbQueryServiceManagerFactory extends AbstractServiceManagerFactory
{
    const CONFIG_KEY = 'db_query_services';

    protected $serviceManagerClass = \Dvsa\Olcs\Api\Domain\DbQueryServiceManager::class;
}

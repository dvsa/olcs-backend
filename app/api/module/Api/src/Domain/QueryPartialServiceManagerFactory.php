<?php

/**
 * QueryPartial Service Manager Factory
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain;

use Dvsa\Olcs\Api\Service\AbstractServiceManagerFactory;

/**
 * QueryPartial Service Manager Factory
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class QueryPartialServiceManagerFactory extends AbstractServiceManagerFactory
{
    const CONFIG_KEY = 'query_partial_services';

    protected $serviceManagerClass = \Dvsa\Olcs\Api\Domain\QueryPartialServiceManager::class;
}

<?php

/**
 * Query Handler Manager Factory
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain;

use Dvsa\Olcs\Api\Service\AbstractServiceManagerFactory;

/**
 * Query Handler Manager Factory
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class QueryHandlerManagerFactory extends AbstractServiceManagerFactory
{
    const CONFIG_KEY = 'query_handlers';
    const PLUGIN_MANAGER_CLASS = QueryHandlerManager::class;
}

<?php

namespace Dvsa\Olcs\Api\Domain;

use Dvsa\Olcs\Api\Domain\Repository\Query\QueryInterface;
use Laminas\ServiceManager\AbstractPluginManager;
use Laminas\ServiceManager\ConfigInterface;

/**
 * @method Repository\Query\AbstractRawQuery get($name) Get the Query service
 */
class DbQueryServiceManager extends AbstractPluginManager
{
    protected $instanceOf = QueryInterface::class;
}

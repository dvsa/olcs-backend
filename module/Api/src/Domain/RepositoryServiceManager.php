<?php

namespace Dvsa\Olcs\Api\Domain;

use Dvsa\Olcs\Api\Domain\Repository\RepositoryInterface;
use Laminas\ServiceManager\AbstractPluginManager;

class RepositoryServiceManager extends AbstractPluginManager
{
    protected $instanceOf = RepositoryInterface::class;
}

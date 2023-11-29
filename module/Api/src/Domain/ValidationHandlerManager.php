<?php

namespace Dvsa\Olcs\Api\Domain;

use Laminas\ServiceManager\AbstractPluginManager;
use Dvsa\Olcs\Api\Domain\Validation\Handlers\HandlerInterface;

class ValidationHandlerManager extends AbstractPluginManager
{
    protected $instanceOf = HandlerInterface::class;
}

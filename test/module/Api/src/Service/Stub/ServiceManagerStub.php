<?php

namespace Dvsa\OlcsTest\Api\Service\Stub;

use Laminas\ServiceManager\AbstractPluginManager;
use Laminas\ServiceManager\Exception;

class ServiceManagerStub extends AbstractPluginManager
{
    /** @SuppressWarnings("unused") */
    public function validatePlugin($plugin)
    {
    }
}

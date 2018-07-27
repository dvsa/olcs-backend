<?php

namespace Dvsa\OlcsTest\Api\Service\Stub;

use Zend\ServiceManager\AbstractPluginManager;
use Zend\ServiceManager\Exception;

class ServiceManagerStub extends AbstractPluginManager
{
    /** @SuppressWarnings("unused") */
    public function validate($plugin)
    {
    }
}

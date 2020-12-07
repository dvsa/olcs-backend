<?php

namespace Dvsa\OlcsTest\Api\Service\Stub;

use Dvsa\Olcs\Api\Service\AbstractServiceManagerFactory;
use Laminas\ServiceManager\ServiceManager;

/**
 * Stub class for testing AbstractServiceManagerFactory
 */
class AbstractServiceManagerFactoryStub extends AbstractServiceManagerFactory
{
    const CONFIG_KEY = 'unit_SrvMngFactory';

    protected $serviceManagerClass = ServiceManagerStub::class;
}

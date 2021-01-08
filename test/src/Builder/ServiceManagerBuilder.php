<?php

namespace Dvsa\OlcsTest\Builder;

use Laminas\ServiceManager\ServiceManager;

class ServiceManagerBuilder implements BuilderInterface
{
    /**
     * @var callable
     */
    protected $servicesProvider;

    /**
     * @param callable $servicesProvider
     */
    public function __construct(callable $servicesProvider)
    {
        $this->servicesProvider = $servicesProvider;
    }

    /**
     * @inheritDoc
     */
    public function build()
    {
        $serviceManager = new ServiceManager();
        $serviceManager->setAllowOverride(true);
        $services = call_user_func($this->servicesProvider, $serviceManager);
        foreach ($services as $serviceName => $service) {
            $serviceManager->setService($serviceName, $service);
        }
        return $serviceManager;
    }
}

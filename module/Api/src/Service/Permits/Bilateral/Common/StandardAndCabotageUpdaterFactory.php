<?php

namespace Dvsa\Olcs\Api\Service\Permits\Bilateral\Common;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

class StandardAndCabotageUpdaterFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return StandardAndCabotageUpdater
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this->__invoke($serviceLocator, StandardAndCabotageUpdater::class);
    }
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new StandardAndCabotageUpdater(
            $container->get('PermitsBilateralCommonModifiedAnswerUpdater')
        );
    }
}

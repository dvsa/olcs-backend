<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Ecmt;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

class ConditionalFeeUpdaterFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return ConditionalFeeUpdater
     */
    public function createService(ServiceLocatorInterface $serviceLocator): ConditionalFeeUpdater
    {
        return $this->__invoke($serviceLocator, ConditionalFeeUpdater::class);
    }

    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return ConditionalFeeUpdater
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): ConditionalFeeUpdater
    {
        return new ConditionalFeeUpdater(
            $container->get('QaEcmtFeeUpdater')
        );
    }
}

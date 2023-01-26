<?php

namespace Dvsa\Olcs\Api\Service\Permits\Fees;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

class EcmtApplicationFeeCommandCreatorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return EcmtApplicationFeeCommandCreator
     */
    public function createService(ServiceLocatorInterface $serviceLocator): EcmtApplicationFeeCommandCreator
    {
        return $this->__invoke($serviceLocator, EcmtApplicationFeeCommandCreator::class);
    }

    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return EcmtApplicationFeeCommandCreator
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): EcmtApplicationFeeCommandCreator
    {
        return new EcmtApplicationFeeCommandCreator(
            $container->get('RepositoryServiceManager')->get('FeeType'),
            $container->get('CqrsCommandCreator'),
            $container->get('CommonCurrentDateTimeFactory')
        );
    }
}

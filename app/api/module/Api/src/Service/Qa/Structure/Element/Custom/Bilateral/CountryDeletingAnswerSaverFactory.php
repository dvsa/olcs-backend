<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Bilateral;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

class CountryDeletingAnswerSaverFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return CountryDeletingAnswerSaver
     */
    public function createService(ServiceLocatorInterface $serviceLocator): CountryDeletingAnswerSaver
    {
        return $this->__invoke($serviceLocator, CountryDeletingAnswerSaver::class);
    }

    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return CountryDeletingAnswerSaver
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): CountryDeletingAnswerSaver
    {
        return new CountryDeletingAnswerSaver(
            $container->get('QaGenericAnswerFetcher'),
            $container->get('QaGenericAnswerWriter'),
            $container->get('QaBilateralClientReturnCodeHandler')
        );
    }
}

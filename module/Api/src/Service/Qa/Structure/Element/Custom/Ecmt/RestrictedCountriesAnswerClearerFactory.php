<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Ecmt;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

class RestrictedCountriesAnswerClearerFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return RestrictedCountriesAnswerClearer
     */
    public function createService(ServiceLocatorInterface $serviceLocator): RestrictedCountriesAnswerClearer
    {
        return $this->__invoke($serviceLocator, RestrictedCountriesAnswerClearer::class);
    }

    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return RestrictedCountriesAnswerClearer
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): RestrictedCountriesAnswerClearer
    {
        return new RestrictedCountriesAnswerClearer(
            $container->get('QaGenericAnswerClearer'),
            $container->get('RepositoryServiceManager')->get('IrhpApplication'),
            $container->get('QaCommonArrayCollectionFactory')
        );
    }
}

<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Ecmt;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

class RestrictedCountriesAnswerSaverFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return RestrictedCountriesAnswerSaver
     */
    public function createService(ServiceLocatorInterface $serviceLocator): RestrictedCountriesAnswerSaver
    {
        return $this->__invoke($serviceLocator, RestrictedCountriesAnswerSaver::class);
    }

    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return RestrictedCountriesAnswerSaver
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): RestrictedCountriesAnswerSaver
    {
        $repoServiceManager = $container->get('RepositoryServiceManager');
        return new RestrictedCountriesAnswerSaver(
            $repoServiceManager->get('IrhpApplication'),
            $repoServiceManager->get('Country'),
            $container->get('QaCommonArrayCollectionFactory'),
            $container->get('QaNamedAnswerFetcher'),
            $container->get('QaGenericAnswerWriter'),
            $container->get('PermitsCommonStockBasedRestrictedCountryIdsProvider')
        );
    }
}

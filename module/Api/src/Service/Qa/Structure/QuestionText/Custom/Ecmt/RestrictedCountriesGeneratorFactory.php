<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\QuestionText\Custom\Ecmt;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

class RestrictedCountriesGeneratorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return RestrictedCountriesGenerator
     */
    public function createService(ServiceLocatorInterface $serviceLocator): RestrictedCountriesGenerator
    {
        return $this->__invoke($serviceLocator, RestrictedCountriesGenerator::class);
    }

    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return RestrictedCountriesGenerator
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): RestrictedCountriesGenerator
    {
        return new RestrictedCountriesGenerator(
            $container->get('QaQuestionTextGenerator')
        );
    }
}

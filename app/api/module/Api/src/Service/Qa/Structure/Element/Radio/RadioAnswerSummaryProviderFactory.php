<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Radio;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

class RadioAnswerSummaryProviderFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return RadioAnswerSummaryProvider
     */
    public function createService(ServiceLocatorInterface $serviceLocator): RadioAnswerSummaryProvider
    {
        return $this->__invoke($serviceLocator, RadioAnswerSummaryProvider::class);
    }

    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return RadioAnswerSummaryProvider
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): RadioAnswerSummaryProvider
    {
        return new RadioAnswerSummaryProvider(
            $container->get('QaOptionListGenerator')
        );
    }
}

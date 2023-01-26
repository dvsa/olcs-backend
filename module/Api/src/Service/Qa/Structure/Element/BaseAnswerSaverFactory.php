<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

class BaseAnswerSaverFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return BaseAnswerSaver
     */
    public function createService(ServiceLocatorInterface $serviceLocator): BaseAnswerSaver
    {
        return $this->__invoke($serviceLocator, BaseAnswerSaver::class);
    }

    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return BaseAnswerSaver
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
public function __invoke(ContainerInterface $container, $requestedName, array $options = null): BaseAnswerSaver
    {
        return new BaseAnswerSaver(
            $container->get('QaGenericAnswerWriter'),
            $container->get('QaGenericAnswerFetcher')
        );
    }
}

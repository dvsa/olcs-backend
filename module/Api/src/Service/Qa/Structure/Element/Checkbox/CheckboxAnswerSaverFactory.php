<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Checkbox;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

class CheckboxAnswerSaverFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return CheckboxAnswerSaver
     */
    public function createService(ServiceLocatorInterface $serviceLocator): CheckboxAnswerSaver
    {
        return $this->__invoke($serviceLocator, CheckboxAnswerSaver::class);
    }

    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return CheckboxAnswerSaver
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): CheckboxAnswerSaver
    {
        return new CheckboxAnswerSaver(
            $container->get('QaGenericAnswerWriter'),
            $container->get('QaGenericAnswerFetcher')
        );
    }
}

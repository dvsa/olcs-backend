<?php

namespace Dvsa\Olcs\Api\Service\Qa\AnswerSaver;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

class GenericAnswerWriterFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return GenericAnswerWriter
     */
    public function createService(ServiceLocatorInterface $serviceLocator): GenericAnswerWriter
    {
        return $this->__invoke($serviceLocator, GenericAnswerWriter::class);
    }

    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return GenericAnswerWriter
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): GenericAnswerWriter
    {
        return new GenericAnswerWriter(
            $container->get('QaGenericAnswerProvider'),
            $container->get('QaAnswerFactory'),
            $container->get('RepositoryServiceManager')->get('Answer')
        );
    }
}

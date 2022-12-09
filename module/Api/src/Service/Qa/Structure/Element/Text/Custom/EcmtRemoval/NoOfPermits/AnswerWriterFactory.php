<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\Element\Text\Custom\EcmtRemoval\NoOfPermits;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

class AnswerWriterFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return AnswerWriter
     */
    public function createService(ServiceLocatorInterface $serviceLocator): AnswerWriter
    {
        return $this->__invoke($serviceLocator, AnswerWriter::class);
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return AnswerWriter
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): AnswerWriter
    {
        return new AnswerWriter(
            $container->get('RepositoryServiceManager')->get('IrhpPermitApplication')
        );
    }
}

<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure\QuestionText\Custom\Bilateral;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Interop\Container\ContainerInterface;

class CabotageGeneratorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return CabotageGenerator
     */
    public function createService(ServiceLocatorInterface $serviceLocator): CabotageGenerator
    {
        return $this->__invoke($serviceLocator, CabotageGenerator::class);
    }

    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return CabotageGenerator
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): CabotageGenerator
    {
        return new CabotageGenerator(
            $container->get('QaQuestionTextGenerator')
        );
    }
}

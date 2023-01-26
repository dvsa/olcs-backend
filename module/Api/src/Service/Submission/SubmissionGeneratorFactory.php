<?php

namespace Dvsa\Olcs\Api\Service\Submission;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Dvsa\Olcs\Api\Service\Submission\Sections\SectionGeneratorPluginManager as SectionGeneratorPluginManager;
use Interop\Container\ContainerInterface;

/**
 * Class SubmissionGeneratorFactory
 * @package Dvsa\Olcs\Api\Service\Submission
 */
class SubmissionGeneratorFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator): SubmissionGenerator
    {
        return $this->__invoke($serviceLocator, SubmissionGenerator::class);
    }

    /**
     * invoke method
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return SubmissionGenerator
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): SubmissionGenerator
    {
        return new SubmissionGenerator(
            $container->get('Config')['submissions'],
            $container->get(SectionGeneratorPluginManager::class)
        );
    }
}

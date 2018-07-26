<?php

namespace Dvsa\Olcs\Api\Service\Submission;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Dvsa\Olcs\Api\Service\Submission\Sections\SectionGeneratorPluginManager as SectionGeneratorPluginManager;

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
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator, self::class);
    }
    
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new SubmissionGenerator(
            $container->get('Config')['submissions'],
            $container->get(SectionGeneratorPluginManager::class)
        );
    }
}

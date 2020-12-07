<?php

namespace Dvsa\Olcs\Api\Service\Submission;

use Laminas\ServiceManager\FactoryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
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
        return new SubmissionGenerator(
            $serviceLocator->get('Config')['submissions'],
            $serviceLocator->get(SectionGeneratorPluginManager::class)
        );
    }
}

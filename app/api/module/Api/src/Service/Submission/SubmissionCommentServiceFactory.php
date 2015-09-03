<?php

namespace Dvsa\Olcs\Api\Service\Submission;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class SubmissionCommentServiceFactory
 * @package Dvsa\Olcs\Api\Service\Submission
 */
class SubmissionCommentServiceFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new SubmissionCommentService(
            $serviceLocator->get('Config')['submissions']['sections']['configuration']
        );
    }
}

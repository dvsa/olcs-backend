<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Application;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;
use Dvsa\Olcs\Api\Entity\User\Permission;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * Publish an application
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class Publish extends AbstractQueryHandler
{
    protected $repoServiceName = 'Application';

    /**
     * @var \Dvsa\Olcs\Api\Service\Lva\Application\PublishValidationService
     */
    private $applicationValidationService;

    /**
     * @var \Dvsa\Olcs\Api\Service\Lva\Variation\PublishValidationService
     */
    private $variationValidationService;


    public function createService(\Laminas\ServiceManager\ServiceLocatorInterface $serviceLocator)
    {
        $mainServiceLocator = $serviceLocator->getServiceLocator();
        $this->applicationValidationService = $mainServiceLocator->get('ApplicationPublishValidationService');
        $this->variationValidationService = $mainServiceLocator->get('VariationPublishValidationService');

        return parent::createService($serviceLocator);
    }

    public function handleQuery(QueryInterface $query)
    {
        /* @var $application ApplicationEntity */
        $application = $this->getRepo()->fetchUsingId($query);

        $validationService = ($application->getIsVariation()) ?
            $this->variationValidationService :
            $this->applicationValidationService;

        return $this->result(
            $application,
            [],
            [
                'hasActiveS4' => $application->hasActiveS4(),
                'existingPublication' => !$application->getPublicationLinks()->isEmpty(),
                'errors' => $validationService->validate($application),
            ]
        );
    }
}

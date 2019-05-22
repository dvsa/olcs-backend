<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\IrhpApplication;

use Dvsa\Olcs\Api\Domain\Exception\NotFoundException;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Service\Qa\ApplicationStepObjectsProvider;
use Dvsa\Olcs\Transfer\Query\Qa\ApplicationStep as ApplicationStepQry;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Application step
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class ApplicationStep extends AbstractQueryHandler
{
    protected $repoServiceName = 'Answer';

    /** @var ApplicationStepObjectsProvider */
    private $applicationStepObjectsProvider;

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator Service Manager
     *
     * @return $this
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $mainServiceLocator = $serviceLocator->getServiceLocator();

        $this->applicationStepObjectsProvider = $mainServiceLocator->get('QaApplicationStepObjectsProvider');

        return parent::createService($serviceLocator);
    }

    /**
     * Handle query
     *
     * @param QueryInterface|ApplicationStepQuery $query query
     *
     * @return array
     */
    public function handleQuery(QueryInterface $query)
    {
        $objects = $this->applicationStepObjectsProvider->getObjects(
            $query->getId(),
            $query->getSlug()
        );

        extract($objects);

        try {
            $answer = $this->getRepo('Answer')->fetchByQuestionIdAndIrhpApplicationId(
                $question->getId(),
                $irhpApplication->getId()
            );
        } catch (NotFoundException $e) {
            $answer = null;
        }

        $templateVars = array_merge(
            $question->getActiveQuestionText()->getTemplateVars(),
            [
                'application' => [
                    'applicationRef' => $irhpApplication->getApplicationRef()
                ]
            ]
        );

        return [
            'form' => $formControlStrategy->getFormRepresentation(
                $applicationStep,
                $irhpApplication,
                $answer
            ),
            'templateVars' => $formControlStrategy->processTemplateVars(
                $applicationStep,
                $irhpApplication,
                $templateVars
            ),
            'nextStepSlug' => $applicationStep->getNextStepSlug()
        ];
    }
}

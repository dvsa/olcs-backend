<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\IrhpApplication;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Service\Qa\ApplicationStepObjectsProvider;
use Dvsa\Olcs\Api\Service\Qa\FormControlStrategyProvider;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Transfer\Command\IrhpApplication\SubmitApplicationStep as SubmitApplicationStepCmd;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Submit application step
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class SubmitApplicationStep extends AbstractCommandHandler
{
    protected $repoServiceName = 'IrhpApplication';

    /** @var ApplicationStepObjectsProvider */
    private $applicationStepObjectsProvider;

    /** @var FormControlStrategyProvider */
    private $formControlStrategyProvider;

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
        $this->formControlStrategyProvider = $mainServiceLocator->get('QaFormControlStrategyProvider');

        return parent::createService($serviceLocator);
    }

    /**
     * Handle command
     *
     * @param SubmitApplicationStepCmd|CommandInterface $command command
     *
     * @return Result
     */
    public function handleCommand(CommandInterface $command)
    {
        $objects = $this->applicationStepObjectsProvider->getObjects(
            $command->getId(),
            $command->getSlug()
        );

        extract($objects);

        $formControlStrategy = $this->formControlStrategyProvider->get($applicationStep);
        $formControlStrategy->saveFormData($applicationStep, $irhpApplication, $command->getPostData());

        // reset check answers and declaration
        $irhpApplication->resetCheckAnswersAndDeclaration();
        $this->getRepo()->save($irhpApplication);

        return $this->result;
    }
}

<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\IrhpApplication;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Service\Qa\FormControlStrategyProvider;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Transfer\Command\IrhpApplication\SubmitApplicationPath as SubmitApplicationPathCmd;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Submit application path
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class SubmitApplicationPath extends AbstractCommandHandler
{
    /** @var FormControlStrategyProvider */
    private $formControlStrategyProvider;

    protected $repoServiceName = 'IrhpApplication';

    protected $extraRepos = ['ApplicationPath'];

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

        $this->formControlStrategyProvider = $mainServiceLocator->get('QaFormControlStrategyProvider');

        return parent::createService($serviceLocator);
    }

    /**
     * Handle command
     *
     * @param SubmitApplicationPathCmd|CommandInterface $command command
     *
     * @return Result
     */
    public function handleCommand(CommandInterface $command)
    {
        $irhpApplication = $this->getRepo()->fetchUsingId($command);

        $applicationPath = $this->getRepo('ApplicationPath')->fetchByIrhpPermitTypeIdAndDate(
            $irhpApplication->getIrhpPermitType()->getId(),
            $irhpApplication->getApplicationPathLockedOn()
        );

        foreach ($applicationPath->getApplicationSteps() as $applicationStep) {
            $formControlStrategy = $this->formControlStrategyProvider->get($applicationStep);
            $formControlStrategy->saveFormData($applicationStep, $irhpApplication, $command->getPostData());
        }

        return $this->result;
    }
}

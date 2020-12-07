<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\IrhpApplication;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Service\Qa\QaContextFactory;
use Dvsa\Olcs\Api\Service\Qa\Facade\SupplementedApplicationSteps\SupplementedApplicationStep;
use Dvsa\Olcs\Api\Service\Qa\Facade\SupplementedApplicationSteps\SupplementedApplicationStepsProvider;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Transfer\Command\IrhpApplication\SubmitApplicationPath as SubmitApplicationPathCmd;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * Submit application path
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class SubmitApplicationPath extends AbstractCommandHandler
{
    /** @var QaContextFactory */
    private $qaContextFactory;

    /** @var SupplementedApplicationStepsProvider */
    private $supplementedApplicationStepsProvider;

    protected $repoServiceName = 'IrhpApplication';

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

        $this->qaContextFactory = $mainServiceLocator->get('QaContextFactory');

        $this->supplementedApplicationStepsProvider = $mainServiceLocator->get(
            'QaSupplementedApplicationStepsProvider'
        );

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

        $supplementedApplicationSteps = $this->supplementedApplicationStepsProvider->get(
            $this->getRepo()->fetchUsingId($command)
        );

        foreach ($supplementedApplicationSteps as $supplementedApplicationStep) {
            $qaContext = $this->qaContextFactory->create(
                $supplementedApplicationStep->getApplicationStep(),
                $irhpApplication
            );

            $supplementedApplicationStep->getFormControlStrategy()->saveFormData(
                $qaContext,
                $command->getPostData()
            );
        }

        return $this->result;
    }
}

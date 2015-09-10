<?php

/**
 * Create Application
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Application;

use Dvsa\Olcs\Api\Domain\Command\Application\CreateApplicationFee as CreateAplicationFeeCommand;
use Dvsa\Olcs\Api\Domain\Command\Application\UpdateApplicationCompletion;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareTrait;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Entity\Application\ApplicationCompletion;
use Dvsa\Olcs\Api\Entity\Application\ApplicationTracking;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation;
use Dvsa\Olcs\Api\Entity\TrafficArea\TrafficArea;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Entity\Application\Application;
use Dvsa\Olcs\Transfer\Command\Application\CreateApplication as Cmd;
use Dvsa\Olcs\Api\Entity\User\Permission;

/**
 * Create Application
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class CreateApplication extends AbstractCommandHandler implements AuthAwareInterface, TransactionedInterface
{
    use AuthAwareTrait;

    protected $repoServiceName = 'Application';

    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();

        $licence = $this->createLicenceObject($command);
        $result->addMessage('Licence created');

        $application = $this->createApplicationObject($command, $licence);
        $result->addMessage('Application created');

        $updatedTol = $this->populateTypeOfLicence($command, $application);

        $this->createApplicationCompletion($application);
        $result->addMessage('Application Completion created');

        $this->createApplicationTracking($application);
        $result->addMessage('Application Tracking created');

        $this->getRepo()->save($application);

        $result->addId('application', $application->getId());
        $result->addId('licence', $licence->getId());

        if ($this->isGranted(Permission::INTERNAL_USER)) {
            $result->merge($this->createTexTask($application));
        }

        if ($updatedTol) {
            $result->merge($this->createApplicationFee($application->getId()));
            $result->merge($this->updateApplicationCompletion($application->getId()));
        }

        return $result;
    }

    private function populateTypeOfLicence(Cmd $command, Application $application)
    {
        if ($command->getNiFlag() !== null
            && $command->getOperatorType() !== null
            && $command->getLicenceType() !== null) {

            $application->updateTypeOfLicence(
                $command->getNiFlag(),
                $this->getRepo()->getRefdataReference($command->getOperatorType()),
                $this->getRepo()->getRefdataReference($command->getLicenceType())
            );

            return true;
        }

        return false;
    }

    private function updateApplicationCompletion($applicationId)
    {
        return $this->getCommandHandler()->handleCommand(
            UpdateApplicationCompletion::create(['id' => $applicationId, 'section' => 'typeOfLicence'])
        );
    }

    private function createApplicationFee($applicationId)
    {
        return $this->getCommandHandler()->handleCommand(CreateAplicationFeeCommand::create(['id' => $applicationId]));
    }

    private function createApplicationCompletion(Application $application)
    {
        $applicationCompletion = new ApplicationCompletion($application);

        $application->setApplicationCompletion($applicationCompletion);
    }

    private function createApplicationTracking(Application $application)
    {
        $applicationTracking = new ApplicationTracking($application);

        $application->setApplicationTracking($applicationTracking);
    }

    /**
     * @param Cmd $command
     * @return Licence
     */
    private function createLicenceObject(Cmd $command)
    {
        $organisation = $this->getRepo()->getReference(Organisation::class, $command->getOrganisation());
        $status = $this->getRepo()->getRefdataReference(Licence::LICENCE_STATUS_NOT_SUBMITTED);

        $licence = new Licence($organisation, $status);

        if ($command->getTrafficArea() !== null) {
            $licence->setTrafficArea(
                $this->getRepo()->getReference(TrafficArea::class, $command->getTrafficArea())
            );
        }

        return $licence;
    }

    /**
     * @param Cmd $command
     * @param Licence $licence
     * @return Application
     */
    private function createApplicationObject(Cmd $command, Licence $licence)
    {
        $application = new Application($licence, $this->getApplicationStatus(), false);

        if ($command->getReceivedDate() !== null) {
            $application->setReceivedDate(new \DateTime($command->getReceivedDate()));
            $application->setTargetCompletionDateFromReceivedDate();
        }

        return $application;
    }

    private function getApplicationStatus()
    {
        if ($this->isGranted(Permission::INTERNAL_USER)) {
            return $this->getRepo()->getRefdataReference(Application::APPLICATION_STATUS_UNDER_CONSIDERATION);
        }

        return $this->getRepo()->getRefdataReference(Application::APPLICATION_STATUS_NOT_SUBMITTED);
    }

    /**
     * Create a TEX task
     *
     * @param ApplicationEntity $application
     *
     * @return Result
     */
    protected function createTexTask(Application $application)
    {
        return $this->handleSideEffect(
            \Dvsa\Olcs\Api\Domain\Command\Application\CreateTexTask::create(
                [
                    'id' => $application->getId(),
                ]
            )
        );
    }
}

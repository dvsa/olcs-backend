<?php

/**
 * Create Application
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Application;

use Dvsa\Olcs\Api\Domain\Command\Application\CreateApplicationFee;
use Dvsa\Olcs\Api\Domain\Command\Application\UpdateApplicationCompletion;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Entity\Application\ApplicationCompletion;
use Dvsa\Olcs\Api\Entity\Application\ApplicationTracking;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Entity\Application\Application;
use Dvsa\Olcs\Transfer\Command\Application\CreateApplication as Cmd;

/**
 * Create Application
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class CreateApplication extends AbstractCommandHandler
{
    protected $repoServiceName = 'Application';

    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();
        try {
            $this->getRepo()->beginTransaction();

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

            if ($updatedTol) {
                $result->merge($this->createApplicationFee($application->getId()));
                $result->merge($this->updateApplicationCompletion($application->getId()));
            }

            $this->getRepo()->commit();

            return $result;

        } catch (\Exception $ex) {
            $this->getRepo()->rollback();

            throw $ex;
        }
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
        return $this->getCommandHandler()->handleCommand(CreateApplicationFee::create(['id' => $applicationId]));
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

        return new Licence($organisation, $status);
    }

    /**
     * @param Cmd $command
     * @param Licence $licence
     * @return Application
     */
    private function createApplicationObject(Cmd $command, Licence $licence)
    {
        return new Application($licence, $this->getApplicationStatus(), false);
    }

    private function getApplicationStatus()
    {
        if ($this->isGranted('internal-view')) {
            return $this->getRepo()->getRefdataReference(Application::APPLICATION_STATUS_UNDER_CONSIDERATION);
        }

        return $this->getRepo()->getRefdataReference(Application::APPLICATION_STATUS_NOT_SUBMITTED);
    }

    /**
     * @TODO Need to replace this with a real way to determine between internal and selfserve users
     *
     * @param $permission
     * @return bool
     */
    private function isGranted($permission)
    {
        return true;
    }
}

<?php

/**
 * Reset Application
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Application;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation;
use Dvsa\Olcs\Api\Entity\Task\Task;
use Dvsa\Olcs\Api\Entity\TrafficArea\TrafficArea;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\Exception;
use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Entity\Application\Application;
use Dvsa\Olcs\Api\Domain\Command\Application\ResetApplication as Cmd;
use Dvsa\Olcs\Transfer\Command\Application\CreateApplication as CreateApplicationCommand;
use Zend\ServiceManager\ServiceLocatorInterface;
use Dvsa\Olcs\Api\Domain\Repository\Licence as LicenceRepo;

/**
 * Reset Application
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class ResetApplication extends AbstractCommandHandler
{
    /**
     * @var LicenceRepo
     */
    private $licenceRepo;

    protected $repoServiceName = 'Application';

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $this->licenceRepo = $serviceLocator->getServiceLocator()->get('RepositoryServiceManager')
            ->get('Licence');

        return parent::createService($serviceLocator);
    }

    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();

        /** @var Application $application */
        $application = $this->getRepo()->fetchUsingId($command, Query::HYDRATE_OBJECT);

        $licence = $application->getLicence();

        $receivedDate = $application->getReceivedDate();
        $trafficArea = $licence->getTrafficArea();

        // Need to grab this now before removing the licence
        $organisation = $application->getLicence()->getOrganisation();

        $this->validate($command, $application);

        try {
            $this->getRepo()->beginTransaction();

            $count = $this->closeTasks($application);
            $result->addMessage($count . ' task(s) closed');

            $this->licenceRepo->delete($licence);
            $result->addMessage('Licence removed');

            $this->getRepo()->delete($application);
            $result->addMessage('Application removed');

            $result->merge($this->createNewApplication($command, $organisation, $receivedDate, $trafficArea));

            $this->getRepo()->commit();

            return $result;
        } catch (\Exception $ex) {
            $this->getRepo()->rollback();
            throw $ex;
        }
    }

    private function createNewApplication(Cmd $command, Organisation $organisation, $receivedDate = null, TrafficArea $trafficArea = null)
    {
        $data = $command->getArrayCopy();
        $data['organisation'] = $organisation->getId();

        if ($receivedDate !== null) {
            if ($receivedDate instanceof \DateTime) {
                $receivedDate = $receivedDate->format('Y-m-d');
            }

            $data['receivedDate'] = $receivedDate;
        }

        if ($trafficArea !== null) {
            $data['trafficArea'] = $trafficArea->getId();
        }

        return $this->getCommandHandler()->handleCommand(
            CreateApplicationCommand::create($data)
        );
    }

    private function closeTasks(Application $application)
    {
        $count = 0;

        /** @var Task $task */
        foreach ($application->getTasks() as $task) {
            if ($task->getIsClosed() === 'N') {
                $count++;
                $task->setIsClosed('Y');
            }
        };

        $this->getRepo()->save($application);

        return $count;
    }

    private function validate(Cmd $command, Application $application)
    {
        if ($command->getConfirm() === false) {
            // Before we tell the UI we need confirmation, we better validate the values
            $application->validateTol(
                $command->getNiFlag(),
                $this->getRepo()->getRefdataReference($command->getOperatorType()),
                $this->getRepo()->getRefdataReference($command->getLicenceType())
            );

            // Tell the UI we need confirmation
            throw new Exception\RequiresConfirmationException(
                'Updating these elements requires confirmation',
                Application::ERROR_REQUIRES_CONFIRMATION
            );
        }

        return true;
    }
}

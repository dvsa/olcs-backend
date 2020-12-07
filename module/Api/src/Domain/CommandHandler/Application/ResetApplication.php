<?php

/**
 * Reset Application
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Application;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Command\Application\ResetApplication as Cmd;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\Exception;
use Dvsa\Olcs\Api\Domain\Repository\Licence as LicenceRepo;
use Dvsa\Olcs\Api\Entity\Application\Application;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Api\Entity\Task\Task;
use Dvsa\Olcs\Transfer\Command\Application\CreateApplication as CreateApplicationCommand;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * Reset Application
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class ResetApplication extends AbstractCommandHandler implements TransactionedInterface
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

        $this->validate($command, $application);

        $licence = $application->getLicence();

        $receivedDate = $application->getReceivedDate();
        $appliedVia = $application->getAppliedVia();

        // Need to grab this now before removing the licence
        $organisation = $application->getLicence()->getOrganisation();

        $count = $this->closeTasks($application);
        $result->addMessage($count . ' task(s) closed');

        $this->licenceRepo->delete($licence);
        $result->addMessage('Licence removed');

        $this->getRepo()->delete($application);
        $result->addMessage('Application removed');

        $result->merge(
            $this->createNewApplication($command, $organisation, $receivedDate, $appliedVia)
        );

        return $result;
    }

    private function createNewApplication(
        Cmd $command,
        Organisation $organisation,
        $receivedDate = null,
        RefData $appliedVia = null
    ) {
        $data = $command->getArrayCopy();
        $data['organisation'] = $organisation->getId();
        $data['appliedVia'] = $appliedVia->getId();

        if ($receivedDate !== null) {
            if ($receivedDate instanceof \DateTime) {
                $receivedDate = $receivedDate->format('Y-m-d');
            }

            $data['receivedDate'] = $receivedDate;
        }

        return $this->handleSideEffect(
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
        }

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

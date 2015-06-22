<?php

/**
 * Submit Application
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Application;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\Command\Task\CreateTask as CreateTaskCmd;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\Exception;
use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Entity\System\Category as CategoryEntity;
use Dvsa\Olcs\Transfer\Command\Application\SubmitApplication as Cmd;
use Dvsa\Olcs\Transfer\Command\CommandInterface;

/**
 * Submit Application
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
final class SubmitApplication extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'Application';

    /**
     * @param CommandInterface $command
     * @return Result
     */
    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();

        /** @var Application $application */
        $application = $this->getRepo()->fetchUsingId($command, Query::HYDRATE_OBJECT, $command->getVersion());

        $this->validate($application);

        $this->snapshotApplication($application);

        $this->updateStatus($application, $result);

        $result->merge($this->createTask($application));

        return $result;
    }

    /**
     * Update the application and licence status (if applicable)
     * @param Application $application
     * @param Result $result
     */
    private function updateStatus($application, $result)
    {
        $now = new \DateTime();
        $newStatus = ApplicationEntity::APPLICATION_STATUS_UNDER_CONSIDERATION;
        $status = $this->getRepo()->getRefdataReference($newStatus);
        $licenceUpdated = false;

        $application
            ->setStatus($status)
            ->setReceivedDate($now)
            ->setTargetCompletionDate($now->modify('+9 week'));

        if (!$application->isVariation()) {
            // update licence status for new apps only, will cascade persist on save
            $licence = $application->getLicence();
            $newLicenceStatus = LicenceEntity::LICENCE_STATUS_UNDER_CONSIDERATION;
            $licence->setStatus($this->getRepo()->getRefdataReference($newLicenceStatus));
            $licenceUpdated = true;
        }

        $this->getRepo()->save($application);

        $result
            ->addId('application', $application->getId())
            ->addMessage('Application updated');

        if ($licenceUpdated) {
             $result
                ->addId('licence', $licence->getId())
                ->addMessage('Licence updated');
        }
    }

    /**
     * @todo call code to generate snapshot, requires OLCS-9586 and possibly
     * others to be completed first
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    private function snapshotApplication(ApplicationEntity $application)
    {

    }

    /**
     * @param ApplicationEntity $application
     * @return Result
     */
    private function createTask(ApplicationEntity $application)
    {
        $now = new \DateTime();
        $actionDate = $now->format('Y-m-d');

        $taskData = [
            'category' => CategoryEntity::CATEGORY_APPLICATION,
            'subCategory' => CategoryEntity::TASK_SUB_CATEGORY_APPLICATION_FORMS_DIGITAL,
            'description' => $this->getTaskDescription($application),
            'actionDate' => $actionDate,
            'application' => $application->getId(),
            'licence' => $application->getLicence()->getId(),
        ];

        return $this->getCommandHandler()->handleCommand(CreateTaskCmd::create($taskData));
    }

    /**
     * @param ApplicationEntity $application
     * @return boolean
     * @throws ValidationException
     */
    private function validate(ApplicationEntity $application)
    {
        if (!$application->canSubmit()) {
            $msg = sprintf(
                "Cannot submit application with status '%s'",
                $application->getStatus()->getDescription()
            );
            throw new Exception\ValidationException([$msg]);
        }

        return true;
    }

    /**
     * @param ApplicationEntity $application
     * @return string
     */
    protected function getTaskDescription(ApplicationEntity $application)
    {
        return $application->getCode() . ' Application';
    }
}

<?php

/**
 * Submit Application
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Application;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Transfer\Command\Application\CreateSnapshot as CreateSnapshotCmd;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\Command\Task\CreateTask as CreateTaskCmd;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\Exception;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
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

        /** @var ApplicationEntity $application */
        $application = $this->getRepo()->fetchUsingId($command, Query::HYDRATE_OBJECT, $command->getVersion());

        $this->validate($application);

        $result->merge($this->snapshotApplication($application));

        $this->updateStatus($application, $result);

        $result->merge($this->createTask($application));

        return $result;
    }

    /**
     * Update the application and licence status (if applicable)
     * @param ApplicationEntity $application
     * @param Result $result
     */
    private function updateStatus(ApplicationEntity $application, $result)
    {
        $now = new DateTime();
        $target = clone $now;
        $target->modify('+9 week');

        $newStatus = ApplicationEntity::APPLICATION_STATUS_UNDER_CONSIDERATION;
        $status = $this->getRepo()->getRefdataReference($newStatus);
        $licenceUpdated = false;

        $application
            ->setStatus($status)
            ->setReceivedDate($now)
            ->setTargetCompletionDate($target);

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

    private function snapshotApplication(ApplicationEntity $application)
    {
        $data = [
            'id' => $application->getId(),
            'event' => CreateSnapshotCmd::ON_SUBMIT
        ];
        return $this->handleSideEffect(CreateSnapshotCmd::create($data));
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
     * @throws Exception\ValidationException
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

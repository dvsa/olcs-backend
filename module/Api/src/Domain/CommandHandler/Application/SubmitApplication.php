<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Application;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareTrait;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\Command\Task\CreateTask as CreateTaskCmd;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\Exception;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Entity\System\Category as CategoryEntity;
use Dvsa\Olcs\Transfer\Command\Application\CreateSnapshot as CreateSnapshotCmd;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use \Dvsa\Olcs\Api\Entity\Application\ApplicationCompletion;

/**
 * Submit Application
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
final class SubmitApplication extends AbstractCommandHandler implements TransactionedInterface, AuthAwareInterface
{
    use AuthAwareTrait;

    protected $repoServiceName = 'Application';

    /**
     * @param \Dvsa\Olcs\Transfer\Command\Application\SubmitApplication $command
     * @return Result
     */
    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();

        /* @var $application ApplicationEntity */
        $application = $this->getRepo()->fetchUsingId($command, Query::HYDRATE_OBJECT, $command->getVersion());

        $this->validate($application);

        $result->merge($this->snapshotApplication($application));

        $this->updateStatus($application, $result);

        $result->merge($this->createTask($application));

        if ($this->shouldPublishApplication($application)) {
            $result->merge($this->createPublication($application));
            $result->merge($this->createTexTask($application));
        }

        return $result;
    }

    /**
     * Should the application be published
     *
     * @param ApplicationEntity $application
     *
     * @return boolean
     */
    private function shouldPublishApplication(ApplicationEntity $application)
    {
        if ($this->isInternalUser()) {
            return false;
        }
        // Exclude for PSV variation applications
        if ($application->isVariation() && $application->isPsv()) {
            return false;
        }

        // Dont publish if application is associated with an S4 whoose outcome is empty or approved
        if ($application->hasActiveS4()) {
            return false;
        }

        return $application->isPublishable();
    }

    /**
     * Update the application and licence status (if applicable)
     * @param ApplicationEntity $application
     * @param Result $result
     */
    private function updateStatus(ApplicationEntity $application, $result)
    {
        $now = new DateTime();

        $newStatus = ApplicationEntity::APPLICATION_STATUS_UNDER_CONSIDERATION;
        $status = $this->getRepo()->getRefdataReference($newStatus);
        $licence = null;

        $application
            ->setStatus($status)
            ->setReceivedDate($now);

        $application->setTargetCompletionDateFromReceivedDate();

        if (!$application->isVariation()) {
            // update licence status for new apps only, will cascade persist on save
            $licence = $application->getLicence();
            $licence->setStatus(
                $this->getRepo()->getRefdataReference(
                    LicenceEntity::LICENCE_STATUS_UNDER_CONSIDERATION
                )
            );
        }

        $this->getRepo()->save($application);

        $result
            ->addId('application', $application->getId())
            ->addMessage('Application updated');

        if ($licence !== null) {
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

        if ($application->isVariation()) {
            $taskData = $this->getVariationTaskdata($application, $taskData);
        }

        return $this->handleSideEffect(CreateTaskCmd::create($taskData));
    }

    /**
     * Modify the task creation data for a variation
     *
     * @param ApplicationEntity $application Application entity
     * @param array             $taskData    Task data
     *
     * @return array
     */
    private function getVariationTaskdata(ApplicationEntity $application, array $taskData)
    {
        // If People is only change section
        if ($this->isOnlyCompletedSection($application, ApplicationCompletion::SECTION_PEOPLE)) {
            // If Ltd company
            if ($application->getLicence()->getOrganisation()->isLtd()) {
                $taskData['subCategory'] = CategoryEntity::TASK_SUB_CATEGORY_DIRECTOR_CHANGE_DIGITAL;
                $taskData['description'] = 'Director change application';
            } else {
                $taskData['subCategory'] = CategoryEntity::TASK_SUB_CATEGORY_PARTNER_CHANGE_DIGITAL;
                $taskData['description'] = 'Partner change application';
            }
            return $taskData;
        }

        // If People is only change section
        if ($this->isOnlyCompletedSection($application, ApplicationCompletion::SECTION_TRANSPORT_MANAGER)) {
            $taskData['subCategory'] = CategoryEntity::TASK_SUB_CATEGORY_APPLICATION_TM1_DIGITAL;
            $taskData['description'] = 'TM change variation';
        }

        return $taskData;
    }

    /**
     * Is a section the only completed section
     *
     * @param ApplicationEntity $application Application entity
     * @param string            $section     Section to test if its the only completed section
     *
     * @return bool
     */
    private function isOnlyCompletedSection(ApplicationEntity $application, $section)
    {
        // These sections are ignored as they always have to be completed
        $ignoredSections = [
            ApplicationCompletion::SECTION_DECLARATION,
            ApplicationCompletion::SECTION_DECLARATION_INTERNAL,
            ApplicationCompletion::SECTION_FINANCIAL_HISTORY,
            ApplicationCompletion::SECTION_CONVICTIONS_AND_PENALTIES,
        ];
        $completionStatuses = $application->getVariationCompletion();

        foreach ($completionStatuses as $completionSection => $status) {
            if (in_array($completionSection, $ignoredSections)) {
                // ignore sections
                continue;
            }
            if ($completionSection === $section) {
                if ($status !== \Dvsa\Olcs\Api\Entity\Application\ApplicationCompletion::STATUS_COMPLETE) {
                    return false;
                }
            } else {
                if ($status === \Dvsa\Olcs\Api\Entity\Application\ApplicationCompletion::STATUS_COMPLETE) {
                    return false;
                }
            }
        }

        return true;
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

    /**
     * Create a publicaction
     *
     * @param ApplicationEntity $application
     *
     * @return Result
     */
    protected function createPublication(ApplicationEntity $application)
    {
        return $this->handleSideEffect(
            \Dvsa\Olcs\Transfer\Command\Publication\Application::create(
                [
                    'id' => $application->getId(),
                    'trafficArea' => $application->getTrafficArea()->getId(),
                ]
            )
        );
    }

    /**
     * Create a TEX task
     *
     * @param ApplicationEntity $application
     *
     * @return Result
     */
    protected function createTexTask(ApplicationEntity $application)
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

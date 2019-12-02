<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Task;

use Doctrine\Common\Collections\Criteria;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Dvsa\Olcs\Api\Entity\Application\Application;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation;
use Dvsa\Olcs\Api\Entity\Task\Task;
use Dvsa\Olcs\Api\Entity\Task\TaskAllocationRule;
use Dvsa\Olcs\Api\Entity\User\Team;
use Dvsa\Olcs\Api\Entity\User\User;
use Dvsa\Olcs\Transfer\Command\CommandInterface;

/**
 * Create Task
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
final class CreateTask extends AbstractCommandHandler
{
    protected $repoServiceName = 'Task';

    protected $extraRepos = ['TaskAllocationRule', 'SystemParameter'];

    protected $alphaNumericTranslations = [
        0 => 'Z',
        1 => 'O',
        2 => 'T',
        3 => 'T',
        4 => 'F',
        5 => 'F',
        6 => 'S',
        7 => 'S',
        8 => 'E',
        9 => 'N'
    ];

    /**
     * Handle command
     *
     * @param \Dvsa\Olcs\Api\Domain\Command\Task\CreateTask $command Command
     *
     * @return Result
     */
    public function handleCommand(CommandInterface $command)
    {
        $task = $this->createTaskObject($command);

        if ($task->getAssignedToUser() === null && $task->getAssignedToTeam() === null) {
            $this->autoAssignTask($task);
        }

        $this->getRepo()->save($task);

        $result = new Result();
        $result->addId('task', $task->getId());
        if ($task->getAssignedToUser() !== null) {
            $result->addId('assignedToUser', $task->getAssignedToUser()->getId());
        }
        $result->addMessage('Task created successfully');

        return $result;
    }

    /**
     * Auto assign task
     *
     * @param Task $task Task
     *
     * @return void
     */
    private function autoAssignTask(Task $task)
    {
        if ($task->getLicence() !== null) {
            $rules = $this->getRulesBasedOnLicence($task);
            $useAlphaSplit = true;
        } else {
            $rules = $this->getRulesBasedOnCategory($task);
            $useAlphaSplit = false;
        }

        /**
         * Multiple rules are just as useless as no rules according to AC
         */
        if (count($rules) !== 1) {
            $this->assignToDefault($task);
        } else {
            $this->assignByRule($task, $rules[0], $useAlphaSplit);
        }
    }

    /**
     * Fall back on system configuration to populate user and team
     *
     * @param Task $task Task
     *
     * @return void
     */
    private function assignToDefault(Task $task)
    {
        /** @var \Dvsa\Olcs\Api\Domain\Repository\SystemParameter $repo */
        $repo = $this->getRepo('SystemParameter');

        $teamId = $repo->fetchValue('task.default_team');
        if ($teamId !== null) {
            $task->setAssignedToTeam($this->getRepo()->getReference(Team::class, $teamId));
        }

        $userId = $repo->fetchValue('task.default_user');
        if ($userId !== null) {
            $task->setAssignedToUser($this->getRepo()->getReference(User::class, $userId));
        }
    }

    /**
     * Assign by rule
     *
     * @param Task               $task          Task
     * @param TaskAllocationRule $rule          TaskAllocationRule
     * @param bool               $useAlphaSplit Should use Alpha split
     *
     * @return void
     */
    protected function assignByRule(Task $task, TaskAllocationRule $rule, $useAlphaSplit)
    {
        $task->setAssignedToTeam($rule->getTeam());
        if ($rule->getUser() !== null) {
            $task->setAssignedToUser($rule->getUser());
        } elseif ($useAlphaSplit) {
            $this->assignByAlphaSplit($task, $rule);
        }
    }

    /**
     * Assign by alpha split
     *
     * @param Task               $task Task
     * @param TaskAllocationRule $rule TaskAllocationRule
     *
     * @return void
     */
    protected function assignByAlphaSplit(Task $task, TaskAllocationRule $rule)
    {
        $taskAlphaSplits = $rule->getTaskAlphaSplits();
        if ($taskAlphaSplits === null) {
            return;
        }
        $letter = $this->getLetterForAlphaSplit($task);
        if (is_numeric($letter)) {
            $letter = $this->alphaNumericTranslations[(int) $letter];
        }
        $criteria = Criteria::create();
        $criteria->andWhere($criteria->expr()->contains('letters', $letter));
        $alphaSplits = $taskAlphaSplits->matching($criteria);
        if (count($alphaSplits) !== 1) {
            return;
        }
        $task->setAssignedToUser($alphaSplits->first()->getUser());
    }

    /**
     * Get rules based on category
     *
     * @param Task $task Task
     *
     * @return array
     */
    protected function getRulesBasedOnCategory(Task $task)
    {
        return $this->getRepo('TaskAllocationRule')->fetchByParameters($task->getCategory()->getId());
    }

    /**
     * Get rules based on licence
     *
     * @param Task $task Task
     *
     * @return array
     */
    protected function getRulesBasedOnLicence(Task $task)
    {
        /** @var \Dvsa\Olcs\Api\Domain\Repository\TaskAllocationRule $repo */
        $repo = $this->getRepo('TaskAllocationRule');

        $licence = $task->getLicence();
        $app = $task->getApplication();

        $licenceTrafficArea = $licence->getTrafficAreaForTaskAllocation();
        $trafficArea = (
            $licenceTrafficArea !== null
            ? $licenceTrafficArea->getId()
            : null
        );
        $category = $task->getCategory();
        $categoryId = (
            $category !== null
            ? $category->getId()
            : null
        );

        //  define operator Type
        $operatorType = null;

        $goodsOrPsv = $licence->getGoodsOrPsv();
        if ($goodsOrPsv === null && $app !== null) {
            $goodsOrPsv = $app->getGoodsOrPsv();
        }

        if ($goodsOrPsv === null) {
            $newApplications = $licence->getNewApplications();
            $app = $newApplications->first();
            $goodsOrPsv = $app->getGoodsOrPsv();
        }

        if ($goodsOrPsv !== null) {
            $operatorType = $goodsOrPsv->getId();
        }

        // Goods Licence
        if ($operatorType === Licence::LICENCE_CATEGORY_GOODS_VEHICLE) {
            $rules = $repo->fetchByParameters(
                $categoryId,
                Licence::LICENCE_CATEGORY_GOODS_VEHICLE,
                $trafficArea,
                $licence->getOrganisation()->isMlh()
            );
            if (count($rules) >= 1) {
                return $rules;
            }
        }

        // PSV licence or no rules found for Goods Licence
        // search rules by category, operator type and traffic area
        $rules = $repo->fetchByParameters(
            $categoryId,
            $operatorType,
            $trafficArea
        );
        if (count($rules) >= 1) {
            return $rules;
        }

        // search rules by category and traffic area
        $rules = $repo->fetchByParameters(
            $categoryId,
            null,
            $trafficArea
        );
        if (count($rules) >= 1) {
            return $rules;
        }

        // search rules by category and operator type
        $rules = $repo->fetchByParameters(
            $categoryId,
            $operatorType
        );
        if (count($rules) >= 1) {
            return $rules;
        }

        // search rules by category only
        return $repo->fetchByParameters($categoryId);
    }

    /**
     * Get letter for alpha split
     *
     * @param Task $task Task
     *
     * @return string
     */
    protected function getLetterForAlphaSplit(Task $task)
    {
        $letter = '';
        $organisation = $task->getLicence()->getOrganisation();
        switch ($organisation->getType()) {
            case Organisation::ORG_TYPE_REGISTERED_COMPANY:
            case Organisation::ORG_TYPE_LLP:
            case Organisation::ORG_TYPE_OTHER:
                $companyName = strtoupper($organisation->getName());
                if (strlen($companyName) > 4 && substr($companyName, 0, 4) === 'THE ') {
                    $companyName = substr($companyName, 4);
                }
                $letter = substr($companyName, 0, 1);
                break;
            case Organisation::ORG_TYPE_SOLE_TRADER:
                $organisationPerson = $organisation->getOrganisationPersons()->first();
                if ($organisationPerson) {
                    $letter = strtoupper(substr($organisationPerson->getPerson()->getFamilyName(), 0, 1));
                }
                break;
            case Organisation::ORG_TYPE_PARTNERSHIP:
                $organisationPersons = $organisation->getOrganisationPersons();
                $criteria = Criteria::create();
                $criteria->orderBy(array('id' => Criteria::ASC));
                // get first person
                $organisationPerson = $organisationPersons->matching($criteria)->first();
                // if first person exists
                if ($organisationPerson) {
                    $letter = strtoupper(substr($organisationPerson->getPerson()->getFamilyName(), 0, 1));
                }
                break;
        }

        return $letter;
    }

    /**
     * Create task object
     *
     * @param \Dvsa\Olcs\Api\Domain\Command\Task\CreateTask $command Command
     *
     * @return Task
     */
    private function createTaskObject(CommandInterface $command)
    {
        /** @var \Dvsa\Olcs\Api\Domain\Repository\Task $repo */
        $repo = $this->getRepo();

        // Required
        $category = $repo->getCategoryReference($command->getCategory());
        $subCategory = $repo->getSubCategoryReference($command->getSubCategory());

        $task = new Task($category, $subCategory);

        // Optional relationships
        $userId = (int)$command->getAssignedToUser();
        $task->setAssignedToUser($repo->getReference(User::class, $userId));
        $task->setAssignedToTeam($repo->getTeamReference((int)$command->getAssignedToTeam(), $userId));

        if ($command->getApplication() !== null) {
            $application = $this->getRepo()->getReference(Application::class, $command->getApplication());
            $task->setApplication($application);
        }

        if ($command->getLicence() !== null) {
            $Licence = $this->getRepo()->getReference(Licence::class, $command->getLicence());
            $task->setLicence($Licence);
        }

        if ($command->getBusReg() !== null) {
            $task->setBusReg(
                $this->getRepo()->getReference(\Dvsa\Olcs\Api\Entity\Bus\BusReg::class, $command->getBusReg())
            );
        }

        if ($command->getCase() !== null) {
            $task->setCase(
                $this->getRepo()->getReference(\Dvsa\Olcs\Api\Entity\Cases\Cases::class, $command->getCase())
            );
        }

        if ($command->getSubmission() !== null) {
            $task->setSubmission(
                $this->getRepo()->getReference(
                    \Dvsa\Olcs\Api\Entity\Submission\Submission::class,
                    $command->getSubmission()
                )
            );
        }

        if ($command->getTransportManager() !== null) {
            $task->setTransportManager(
                $this->getRepo()->getReference(
                    \Dvsa\Olcs\Api\Entity\Tm\TransportManager::class,
                    $command->getTransportManager()
                )
            );
        }

        if ($command->getSurrender() !== null) {
            $task->setSurrender(
                $this->getRepo()->getReference(
                    \Dvsa\Olcs\Api\Entity\Surrender::class,
                    $command->getSurrender()
                )
            );
        }

        if ($command->getIrfoOrganisation() !== null) {
            $task->setIrfoOrganisation(
                $this->getRepo()->getReference(
                    \Dvsa\Olcs\Api\Entity\Organisation\Organisation::class,
                    $command->getIrfoOrganisation()
                )
            );
        }

        if ($command->getEcmtPermitApplication() !== null) {
            $task->setEcmtPermitApplication(
                $this->getRepo()->getReference(
                    \Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication::class,
                    $command->getEcmtPermitApplication()
                )
            );
        }

        if ($command->getIrhpApplication() !== null) {
            $task->setIrhpApplication(
                $this->getRepo()->getReference(
                    \Dvsa\Olcs\Api\Entity\Permits\IrhpApplication::class,
                    $command->getIrhpApplication()
                )
            );
        }

        if ($command->getAssignedByUser() !== null) {
            $task->setAssignedByUser(
                $this->getRepo()->getReference(
                    \Dvsa\Olcs\Api\Entity\User\User::class,
                    $command->getAssignedByUser()
                )
            );
        }

        if ($command->getActionDate() !== null) {
            $task->setActionDate(new DateTime($command->getActionDate()));
        } else {
            $task->setActionDate(new DateTime());
        }

        // Task properties
        $task->setDescription($command->getDescription());
        $task->setIsClosed($command->getIsClosed());
        $task->setUrgent($command->getUrgent());

        $task->setLastModifiedOn(new DateTime());

        return $task;
    }
}

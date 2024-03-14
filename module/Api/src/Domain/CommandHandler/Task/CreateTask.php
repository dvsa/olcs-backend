<?php

declare(strict_types=1);

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Task;

use Doctrine\Common\Collections\Criteria;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\Repository;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Dvsa\Olcs\Api\Entity\Application\Application;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation;
use Dvsa\Olcs\Api\Entity\Task\Task;
use Dvsa\Olcs\Api\Entity\Task\TaskAllocationRule;
use Dvsa\Olcs\Api\Entity\User\Team;
use Dvsa\Olcs\Api\Entity\User\User;
use Dvsa\Olcs\Transfer\Command\CommandInterface;

final class CreateTask extends AbstractCommandHandler
{
    protected $repoServiceName = Repository\Task::class;

    protected $extraRepos = [
        Repository\TaskAllocationRule::class,
        Repository\SystemParameter::class,
    ];

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

    public function handleCommand(CommandInterface $command): Result
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

    private function autoAssignTask(Task $task): void
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
     */
    private function assignToDefault(Task $task): void
    {
        $repo = $this->getRepo(Repository\SystemParameter::class);

        $teamId = $repo->fetchValue('task.default_team');
        if ($teamId !== null) {
            $task->setAssignedToTeam($this->getRepo()->getReference(Team::class, $teamId));
        }

        $userId = $repo->fetchValue('task.default_user');
        if ($userId !== null) {
            $task->setAssignedToUser($this->getRepo()->getReference(User::class, $userId));
        }
    }

    protected function assignByRule(Task $task, TaskAllocationRule $rule, bool $useAlphaSplit): void
    {
        $task->setAssignedToTeam($rule->getTeam());
        if ($rule->getUser() !== null) {
            $task->setAssignedToUser($rule->getUser());
        } elseif ($useAlphaSplit) {
            $this->assignByAlphaSplit($task, $rule);
        }
    }

    protected function assignByAlphaSplit(Task $task, TaskAllocationRule $rule): void
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

    protected function getRulesBasedOnCategory(Task $task): array
    {
        return $this
            ->getRepo(Repository\TaskAllocationRule::class)
            ->fetchByParameters(
                $task->getCategory()->getId(),
                $task->getSubCategory()->getId(),
                null,
                null,
                null
            );
    }

    protected function getRulesBasedOnLicence(Task $task): array
    {
        $repo = $this->getRepo(Repository\TaskAllocationRule::class);

        $licence = $task->getLicence();
        $app = $task->getApplication();

        $licenceTrafficArea = $licence->getTrafficAreaForTaskAllocation();
        $trafficArea = (
            $licenceTrafficArea !== null
            ? $licenceTrafficArea->getId()
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
                $task->getCategory()->getId(),
                $task->getSubCategory()->getId(),
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
            $task->getCategory()->getId(),
            $task->getSubCategory()->getId(),
            $operatorType,
            $trafficArea,
            null
        );
        if (count($rules) >= 1) {
            return $rules;
        }

        // search rules by category and traffic area
        $rules = $repo->fetchByParameters(
            $task->getCategory()->getId(),
            $task->getSubCategory()->getId(),
            null,
            $trafficArea,
            null
        );
        if (count($rules) >= 1) {
            return $rules;
        }

        // search rules by category and operator type
        $rules = $repo->fetchByParameters(
            $task->getCategory()->getId(),
            $task->getSubCategory()->getId(),
            $operatorType,
            null,
            null
        );
        if (count($rules) >= 1) {
            return $rules;
        }

        // search rules by category only
        return $repo->fetchByParameters($task->getCategory()->getId(), $task->getSubCategory()->getId(), null, null, null);
    }

    protected function getLetterForAlphaSplit(Task $task): string
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

    private function createTaskObject(CommandInterface $command): Task
    {
        /** @var Repository\Task $repo */
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
        $task->setMessaging(in_array($command->getMessaging(), ['Y', true]));

        $task->setLastModifiedOn(new DateTime());

        return $task;
    }
}

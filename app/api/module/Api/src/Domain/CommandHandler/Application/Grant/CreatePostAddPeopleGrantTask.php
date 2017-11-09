<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Application\Grant;

use Dvsa\Olcs\Api\Domain\Command\Application\Grant\CreatePostAddPeopleGrantTask as CreatePostAddPeopleGrantTaskCommand;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\Command\Task\CreateTask;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Entity\Application\Application;
use Dvsa\Olcs\Api\Entity\System\Category;
use Dvsa\Olcs\Transfer\Command\CommandInterface;

/**
 * Class CreatePostDeletePeopleGrantTask
 */
final class CreatePostAddPeopleGrantTask extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'Application';

    /**
     * Handle command
     *
     * @param CreatePostAddPeopleGrantTaskCommand|CommandInterface $command command
     *
     * @return Result
     */
    public function handleCommand(CommandInterface $command)
    {
        /** @var Application $application */
        $application = $this->getRepo()->fetchById($command->getApplicationId());

        $variationType = $application->getVariationType();
        if ($variationType === null or $variationType->getId() !== Application::VARIATION_TYPE_DIRECTOR_CHANGE) {
            return $this->result;
        }

        $applicationOrgPeople = $application->getApplicationOrganisationPersons();

        if ($applicationOrgPeople < 1) {
            return $this->result;
        }

        $this->result->addMessage('Task created as new people were added');


        $this->handleSideEffect(
            CreateTask::create(
                [
                    'category' => Category::CATEGORY_APPLICATION,
                    'subCategory' => Category::TASK_SUB_CATEGORY_PERSON_CHANGE_DIGITAL,
                    'description' => 'Add director(s)',
                    'licence' => $application->getLicence()->getId(),
                    'urgent' => true,
                ]
            )
        );
        return $this->result;
    }
}

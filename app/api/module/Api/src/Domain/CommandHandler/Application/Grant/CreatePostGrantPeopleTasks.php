<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Application\Grant;

use Dvsa\Olcs\Api\Domain\Command\Task\CreateTask;
use Dvsa\Olcs\Api\Entity\System\Category;
use Dvsa\Olcs\Api\Domain\Command\Application\Grant\CreatePostGrantPeopleTasks as CreatePostGrantPeopleTasksCommand;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\Repository\OrganisationPerson;
use Dvsa\Olcs\Api\Entity\Application\Application;
use Dvsa\Olcs\Transfer\Command\CommandInterface;

/**
 * Create Post Grant People Tasks
 */
final class CreatePostGrantPeopleTasks extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'Application';

    protected $extraRepos = ['OrganisationPerson'];

    /**
     * Handle command
     *
     * @param CreatePostGrantPeopleTasksCommand|CommandInterface $command
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

        /** @var OrganisationPerson $organisationPersonRepository */
        $organisationPersonRepository = $this->getRepo('OrganisationPerson');

        $personCount = $organisationPersonRepository->fetchCountForOrganisation(
            $application->getLicence()->getOrganisation()->getId()
        );

        if ($personCount == 0) {
            $this->handleSideEffect(
                CreateTask::create(
                    [
                        'category' => Category::CATEGORY_APPLICATION,
                        'subCategory' => Category::TASK_SUB_CATEGORY_DIRECTOR_CHANGE_DIGITAL,
                        'description' => 'Last person removed',
                        'licence' => $application->getLicence()->getId(),
                    ]
                )
            );
            $this->result->addMessage('Task created as there are no people in the organisation');
        }

        return $this->result;
    }
}

<?php

namespace Dvsa\Olcs\Api\Service\Permits\Checkable;

use Dvsa\Olcs\Api\Domain\Command\Task\CreateTask;

class CreateTaskCommandGenerator
{
    /** @var CreateTaskCommandFactory */
    private $createTaskCommandFactory;

    /**
     * Create service instance
     *
     * @param CreateTaskCommandFactory $createTaskCommandFactory
     *
     * @return CreateTaskCommandGenerator
     */
    public function __construct(CreateTaskCommandFactory $createTaskCommandFactory)
    {
        $this->createTaskCommandFactory = $createTaskCommandFactory;
    }

    /**
     * Get a CreateTask command instance representing the application submission task for the provided application
     *
     * @param CheckableApplicationInterface $application
     *
     * @return CreateTask
     */
    public function generate(CheckableApplicationInterface $application)
    {
        $params = [
            'category' => SubmissionTaskProperties::CATEGORY,
            'subCategory' => SubmissionTaskProperties::SUBCATEGORY,
            'description' => $application->getSubmissionTaskDescription(),
            $application->getCamelCaseEntityName() => $application->getId(),
            'licence' => $application->getLicence()->getId()
        ];

        return $this->createTaskCommandFactory->create($params);
    }
}

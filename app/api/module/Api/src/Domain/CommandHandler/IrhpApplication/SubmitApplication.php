<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\IrhpApplication;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\QueueAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\EventHistory\EventHistoryType as EventHistoryTypeEntity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication;
use Dvsa\Olcs\Api\Entity\Queue\Queue;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Api\Service\EventHistory\Creator as EventHistoryCreator;
use Dvsa\Olcs\Api\Service\Permits\Checkable\CreateTaskCommandGenerator;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Transfer\Command\IrhpApplication\SubmitApplication as SubmitApplicationCmd;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Command Handler to action the submission of an IrhpApplication
 *
 * @author Andy Newton <andy@vitri.ltd>
 */
final class SubmitApplication extends AbstractCommandHandler implements ToggleRequiredInterface
{
    use QueueAwareTrait, ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::BACKEND_PERMITS];

    protected $repoServiceName = 'IrhpApplication';

    /** @var CreateTaskCommandGenerator */
    private $createTaskCommandGenerator;

    /** @var EventHistoryCreator */
    private $eventHistoryCreator;

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator Service Manager
     *
     * @return $this
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $mainServiceLocator = $serviceLocator->getServiceLocator();

        $this->createTaskCommandGenerator = $mainServiceLocator->get('PermitsCheckableCreateTaskCommandGenerator');
        $this->eventHistoryCreator = $mainServiceLocator->get('EventHistoryCreator');

        return parent::createService($serviceLocator);
    }

    /**
     * Handle command
     *
     * @param SubmitApplicationCmd|CommandInterface $command command
     *
     * @return Result
     */
    public function handleCommand(CommandInterface $command)
    {
        /** @var IrhpApplication $irhpApplication */
        $irhpApplicationId = $command->getId();
        $irhpApplication = $this->getRepo()->fetchById($irhpApplicationId);

        $irhpApplication->submit(
            $this->refData($irhpApplication->getSubmissionStatus())
        );

        $this->getRepo()->save($irhpApplication);

        // create Event History record
        $this->eventHistoryCreator->create($irhpApplication, EventHistoryTypeEntity::IRHP_APPLICATION_SUBMITTED);

        $sideEffects = [];

        if ($irhpApplication->shouldAllocatePermitsOnSubmission()) {
            $sideEffects[] = $this->createQueue(
                $irhpApplicationId,
                Queue::TYPE_IRHP_APPLICATION_PERMITS_ALLOCATE,
                []
            );
        }

        $sideEffects[] = $this->createTaskCommandGenerator->generate($irhpApplication);

        $sideEffects[] = $this->createQueue(
            $irhpApplicationId,
            Queue::TYPE_PERMITS_POST_SUBMIT,
            ['irhpPermitType' => $irhpApplication->getIrhpPermitType()->getId()]
        );

        $this->result->merge(
            $this->handleSideEffects($sideEffects)
        );

        $this->result->addMessage('IRHP application submitted');
        $this->result->addId('irhpApplication', $irhpApplicationId);

        return $this->result;
    }
}

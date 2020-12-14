<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\IrhpApplication;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\Exception\NotFoundException;
use Dvsa\Olcs\Api\Domain\Exception\RuntimeException;
use Dvsa\Olcs\Api\Domain\Repository\IrhpApplication as IrhpApplicationRepo;
use Dvsa\Olcs\Api\Entity\EventHistory\EventHistoryType as EventHistoryTypeEntity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication as IrhpApplicationEntity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitType as IrhpPermitTypeEntity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitType;
use Dvsa\Olcs\Api\Service\EventHistory\Creator as EventHistoryCreator;
use Dvsa\Olcs\Api\Service\Permits\Bilateral\Internal\ApplicationUpdater as BilateralApplicationUpdater;
use Dvsa\Olcs\Api\Service\Permits\Checkable\CheckedValueUpdater;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Transfer\Command\IrhpApplication\UpdateFull as Cmd;
use Dvsa\Olcs\Transfer\Command\IrhpApplication\UpdateCountries;
use Dvsa\Olcs\Transfer\Command\IrhpApplication\UpdateMultipleNoOfPermits;
use Dvsa\Olcs\Transfer\Command\IrhpApplication\SubmitApplicationPath;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * Create Irhp Permit Application
 */
final class UpdateFull extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'IrhpApplication';

    /** @var CheckedValueUpdater */
    private $checkedValueUpdater;

    /** @var EventHistoryCreator */
    private $eventHistoryCreator;

    /** @var BilateralApplicationUpdater */
    private $bilateralApplicationUpdater;

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

        $this->checkedValueUpdater = $mainServiceLocator->get('PermitsCheckableCheckedValueUpdater');
        $this->eventHistoryCreator = $mainServiceLocator->get('EventHistoryCreator');
        $this->bilateralApplicationUpdater = $mainServiceLocator->get('PermitsBilateralInternalApplicationUpdater');

        return parent::createService($serviceLocator);
    }

    /**
     * Handle command
     *
     * @param Cmd $command command
     *
     * @return Result
     * @throws NotFoundException
     * @throws RuntimeException
     */
    public function handleCommand(CommandInterface $command)
    {
        /** @var IrhpApplicationRepo $irhpApplicationRepo */
        $irhpApplicationRepo = $this->getRepo();

        /** @var IrhpApplicationEntity $irhpApplication */
        $irhpApplication = $irhpApplicationRepo->fetchById($command->getId());

        if ($irhpApplication->isApplicationPathEnabled()) {
            $this->handleSideEffect(
                SubmitApplicationPath::create(
                    [
                        'id' => $irhpApplication->getId(),
                        'postData' => $command->getPostData()
                    ]
                )
            );
        } else {
            $irhpPermitTypeId = $irhpApplication->getIrhpPermitType()->getId();

            $this->updateCountries($irhpApplication, $irhpPermitTypeId, $command);
            $irhpApplicationRepo->refresh($irhpApplication);
            $irhpApplication->resetSectionCompletion();

            $this->updatePermitCounts($irhpApplication, $irhpPermitTypeId, $command);

            $irhpApplicationRepo->refresh($irhpApplication);
            $irhpApplication->resetSectionCompletion();
        }

        if ($command->getDeclaration()) {
            $irhpApplication->updateCheckAnswers();
            $irhpApplicationRepo->save($irhpApplication);
            $irhpApplicationRepo->refresh($irhpApplication);
            $irhpApplication->resetSectionCompletion();
            $irhpApplication->makeDeclaration();
            $irhpApplicationRepo->save($irhpApplication);
        }

        $this->checkedValueUpdater->updateIfRequired($irhpApplication, $command->getChecked());

        $irhpApplication->updateDateReceived($command->getDateReceived());
        $irhpApplicationRepo->save($irhpApplication);

        // create Event History record
        $this->eventHistoryCreator->create($irhpApplication, EventHistoryTypeEntity::IRHP_APPLICATION_UPDATED);

        $this->result->addId('irhpApplication', $irhpApplication->getId());
        $this->result->addMessage('IRHP Application updated successfully');

        return $this->result;
    }

    /**
     * Creates and saves instances of IrhpPermitApplication as required to accompany the IrhpApplication
     *
     * @param IrhpApplicationEntity $irhpApplication
     * @param int $permitTypeId
     * @param CommandInterface $command
     */
    private function updateCountries(IrhpApplicationEntity $irhpApplication, $permitTypeId, CommandInterface $command)
    {
        if ((int)$permitTypeId !== IrhpPermitTypeEntity::IRHP_PERMIT_TYPE_ID_BILATERAL) {
            return;
        }

        $this->result->merge(
            $this->handleSideEffect(
                UpdateCountries::create([
                    'id' => $irhpApplication->getId(),
                    'countries' => array_keys($command->getPermitsRequired())
                ])
            )
        );
    }

    /**
     * Update the permit counts against the specified application
     *
     * @param IrhpApplicationEntity $irhpApplication
     * @param int $permitTypeId
     * @param CommandInterface $command
     */
    private function updatePermitCounts(IrhpApplicationEntity $irhpApplication, $permitTypeId, CommandInterface $command)
    {
        switch ($permitTypeId) {
            case IrhpPermitTypeEntity::IRHP_PERMIT_TYPE_ID_BILATERAL:
                $this->bilateralApplicationUpdater->update(
                    $irhpApplication,
                    $command->getPermitsRequired()
                );
                break;
            case IrhpPermitTypeEntity::IRHP_PERMIT_TYPE_ID_MULTILATERAL:
                $this->result->merge(
                    $this->handleSideEffect(
                        UpdateMultipleNoOfPermits::create([
                            'id' => $irhpApplication->getId(),
                            'permitsRequired' => $command->getPermitsRequired()
                        ])
                    )
                );
                break;
            default:
                throw new RuntimeException('Unsupported permit type ' . $permitTypeId);
        }
    }
}

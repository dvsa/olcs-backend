<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\IrhpApplication;

use DateTime;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\Exception\NotFoundException;
use Dvsa\Olcs\Api\Domain\Repository\IrhpApplication as IrhpApplicationRepo;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\EventHistory\EventHistoryType as EventHistoryTypeEntity;
use Dvsa\Olcs\Api\Entity\IrhpInterface;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication as IrhpApplicationEntity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication as IrhpPermitApplicationEntity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitType as IrhpPermitTypeEntity;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Api\Service\EventHistory\Creator as EventHistoryCreator;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Transfer\Command\IrhpApplication\CreateFull as Cmd;
use Dvsa\Olcs\Transfer\Command\IrhpApplication\UpdateCountries;
use Dvsa\Olcs\Transfer\Command\IrhpApplication\UpdateMultipleNoOfPermits;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Create Irhp Permit Application
 */
final class CreateFull extends AbstractCommandHandler implements ToggleRequiredInterface, TransactionedInterface
{
    use ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::BACKEND_PERMITS];
    protected $repoServiceName = 'IrhpApplication';
    protected $extraRepos = ['IrhpPermitWindow', 'IrhpPermitApplication'];

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

        $this->eventHistoryCreator = $mainServiceLocator->get('EventHistoryCreator');

        return parent::createService($serviceLocator);
    }

    /**
     * Handle command
     *
     * @param Cmd $command command
     *
     * @return Result
     * @throws NotFoundException
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     */
    public function handleCommand(CommandInterface $command)
    {
        /** @var IrhpApplicationRepo $irhpApplicationRepo */
        $irhpApplicationRepo = $this->getRepo();
        /** @var IrhpPermitTypeEntity $permitType */
        $permitType = $irhpApplicationRepo->getReference(IrhpPermitTypeEntity::class, $command->getIrhpPermitType());

        if (!($permitType instanceof IrhpPermitTypeEntity)) {
            throw new NotFoundException('Permit type not found');
        }

        $irhpApplication = IrhpApplicationEntity::createNew(
            $this->refData(IrhpInterface::SOURCE_INTERNAL),
            $this->refData(IrhpInterface::STATUS_NOT_YET_SUBMITTED),
            $permitType,
            $irhpApplicationRepo->getReference(LicenceEntity::class, $command->getLicence()),
            $command->getDateReceived()
        );
        $irhpApplicationRepo->save($irhpApplication);

        // create Event History record
        $this->eventHistoryCreator->create($irhpApplication, EventHistoryTypeEntity::IRHP_APPLICATION_CREATED);

        $this->createIrhpPermitApplications($irhpApplication, $command->getIrhpPermitType());
        $this->updateCountries($irhpApplication, $permitType->getId(), $command);

        $irhpApplicationRepo->refresh($irhpApplication);
        $irhpApplication->resetSectionCompletion();

        $this->result->merge(
            $this->handleSideEffect(
                UpdateMultipleNoOfPermits::create([
                    'id' => $irhpApplication->getId(),
                    'permitsRequired' => $command->getPermitsRequired()
                ])
            )
        );
        $irhpApplicationRepo->refresh($irhpApplication);
        $irhpApplication->resetSectionCompletion();

        if ($command->getDeclaration()) {
            $irhpApplication->updateCheckAnswers();
            $irhpApplicationRepo->save($irhpApplication);
            $irhpApplicationRepo->refresh($irhpApplication);
            $irhpApplication->resetSectionCompletion();

            $irhpApplication->makeDeclaration();
            $irhpApplicationRepo->save($irhpApplication);
        }

        $this->result->addId('irhpApplication', $irhpApplication->getId());
        $this->result->addMessage('IRHP Application created successfully');

        return $this->result;
    }

    /**
     * Creates and saves instances of IrhpPermitApplication as required to accompany the IrhpApplication
     *
     * @param IrhpApplicationEntity $irhpApplication
     * @param int $permitTypeId
     */
    private function updateCountries(IrhpApplicationEntity $irhpApplication, $permitTypeId, $command)
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
     * Creates and saves instances of IrhpPermitApplication as required to accompany the IrhpApplication
     *
     * @param IrhpApplicationEntity $irhpApplication
     * @param int $permitTypeId
     */
    private function createIrhpPermitApplications(IrhpApplicationEntity $irhpApplication, $permitTypeId)
    {
        if ($permitTypeId != IrhpPermitTypeEntity::IRHP_PERMIT_TYPE_ID_MULTILATERAL) {
            return;
        }

        $irhpPermitWindows = $this->getRepo('IrhpPermitWindow')->fetchOpenWindowsByType(
            $permitTypeId,
            new DateTime()
        );

        $irhpPermitApplicationRepo = $this->getRepo('IrhpPermitApplication');

        foreach ($irhpPermitWindows as $irhpPermitWindow) {
            $irhpPermitApplication = IrhpPermitApplicationEntity::createNewForIrhpApplication(
                $irhpApplication,
                $irhpPermitWindow
            );

            $irhpPermitApplicationRepo->save($irhpPermitApplication);
        }
    }
}

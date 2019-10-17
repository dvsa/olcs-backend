<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\IrhpApplication;

use Dvsa\Olcs\Api\Domain\Command\IrhpApplication\CreateDefaultIrhpPermitApplications;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\Exception\NotFoundException;
use Dvsa\Olcs\Api\Domain\Repository\IrhpApplication as IrhpApplicationRepo;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\IrhpInterface;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication as IrhpApplicationEntity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitType as IrhpPermitTypeEntity;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Transfer\Command\Permits\CreateEcmtPermitApplication;

/**
 * Create Irhp Permit Application
 */
final class Create extends AbstractCommandHandler implements ToggleRequiredInterface
{
    use ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::BACKEND_PERMITS];
    protected $repoServiceName = 'IrhpApplication';

    /**
     * Handle command
     *
     * @param CommandInterface $command command
     *
     * @return Result
     * @throws NotFoundException
     */
    public function handleCommand(CommandInterface $command)
    {
        $permitTypeId = $command->getIrhpPermitType();

        /** @var IrhpApplicationRepo $irhpApplicationRepo */
        $irhpApplicationRepo = $this->getRepo();
        /** @var IrhpPermitTypeEntity $permitType */
        $permitType = $irhpApplicationRepo->getReference(IrhpPermitTypeEntity::class, $permitTypeId);

        if (!($permitType instanceof IrhpPermitTypeEntity)) {
            throw new NotFoundException('Permit type not found');
        }

        if ($permitType->isEcmtAnnual()) {
            $this->result->merge(
                $this->handleSideEffect(
                    CreateEcmtPermitApplication::create(
                        $command->getArrayCopy()
                    )
                )
            );
            $this->result->addId('ecmtPermitApplication', $this->result->getIds()['ecmtPermitApplication']);
        } else {
            $source = $command->getFromInternal() ? IrhpInterface::SOURCE_INTERNAL : IrhpInterface::SOURCE_SELFSERVE;
            $irhpApplication = IrhpApplicationEntity::createNew(
                $this->refData($source),
                $this->refData(IrhpInterface::STATUS_NOT_YET_SUBMITTED),
                $permitType,
                $irhpApplicationRepo->getReference(LicenceEntity::class, $command->getLicence()),
                date('Y-m-d')
            );

            $irhpApplicationRepo->save($irhpApplication);

            $this->result->merge(
                $this->handleSideEffect(
                    CreateDefaultIrhpPermitApplications::create(
                        [
                            'id' => $irhpApplication->getId(),
                            'irhpPermitStock' => $command->getIrhpPermitStock()
                        ]
                    )
                )
            );

            $this->result->addId('irhpApplication', $irhpApplication->getId());
            $this->result->addMessage('IRHP Application created successfully');
        }

        return $this->result;
    }
}

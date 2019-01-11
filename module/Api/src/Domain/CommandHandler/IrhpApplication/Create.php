<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\IrhpApplication;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\Exception\NotFoundException;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication as IrhpApplicationEntity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitType as IrhpPermitTypeEntity;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Transfer\Command\IrhpApplication\Create as Cmd;
use Olcs\Logging\Log\Logger;

/**
 * Create Irhp Permit Application
 */
final class Create extends AbstractCommandHandler implements ToggleRequiredInterface
{
    use ToggleAwareTrait;

    const SOURCE_SELFSERVE = 'app_source_selfserve';
    const STATUS_NOT_YET_SUBMITTED = 'permit_app_nys';

    protected $toggleConfig = [FeatureToggle::BACKEND_ECMT];

    protected $repoServiceName = 'IrhpApplication';

    /**
     * Handle command
     *
     * @param Cmd $command command
     *
     * @return Result
     * @throws NotFoundException
     */
    public function handleCommand(CommandInterface $command)
    {
        /** @var IrhpPermitTypeEntity $permitType */
        $permitType = $this->getRepo()->getReference(IrhpPermitTypeEntity::class, $command->getType());

        if (!($permitType instanceof IrhpPermitTypeEntity)) {
            throw new NotFoundException('Permit type not found');
        }

        $irhpApplication = IrhpApplicationEntity::createNew(
            $this->getRepo()->getRefDataReference(self::SOURCE_SELFSERVE),
            $this->getRepo()->getRefdataReference(self::STATUS_NOT_YET_SUBMITTED),
            $this->getRepo()->getReference(IrhpPermitTypeEntity::class, $permitType->getId()),
            $this->getRepo()->getReference(LicenceEntity::class, $command->getLicence()),
            date('Y-m-d')
        );

        $this->getRepo()->save($irhpApplication);

        $this->result->addId('irhpApplication', $irhpApplication->getId());
        $this->result->addMessage('IRHP Application created successfully');

        return $this->result;
    }
}

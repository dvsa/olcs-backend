<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\IrhpPermitApplication;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\Exception\NotFoundException;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication as EcmtPermitApplicationEntity;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication as IrhpApplicationEntity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication as IrhpPermitApplicationEntity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitType as IrhpPermitTypeEntity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitWindow as IrhpPermitWindowEntity;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Transfer\Command\IrhpPermitApplication\Create as Cmd;

/**
 * Create Irhp Permit Application
 */
final class Create extends AbstractCommandHandler implements ToggleRequiredInterface
{
    use ToggleAwareTrait;

    const SOURCE_SELFSERVE = 'app_source_selfserve';
    const STATUS_NOT_YET_SUBMITTED = 'permit_app_nys';

    protected $toggleConfig = [FeatureToggle::BACKEND_ECMT];

    protected $repoServiceName = 'IrhpPermitApplication';

    protected $extraRepos = ['EcmtPermitApplication', 'IrhpApplication', 'IrhpPermitWindow', 'IrhpPermitStock'];

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

        $ecmtPermitApplication = null;
        $irhpApplication = null;

        if ($permitType->getName() == EcmtPermitApplicationEntity::PERMIT_TYPE) {
            $ecmtPermitApplication = EcmtPermitApplication::createNew(
                $this->getRepo()->getRefDataReference(self::SOURCE_SELFSERVE),
                $this->getRepo()->getRefdataReference(self::STATUS_NOT_YET_SUBMITTED),
                $this->getRepo()->getRefdataReference($permitType->getName()),
                $this->getRepo()->getReference(LicenceEntity::class, $command->getLicence()),
                date('Y-m-d')
            );

            $this->getRepo('EcmtPermitApplication')->save($ecmtPermitApplication);

            $this->result->addId('ecmtPermitApplication', $ecmtPermitApplication->getId());
            $this->result->addMessage('ECMT Permit Application created successfully');
        } else {
            $irhpApplication = IrhpApplicationEntity::createNew(
                $this->getRepo()->getRefDataReference(self::SOURCE_SELFSERVE),
                $this->getRepo()->getRefdataReference(self::STATUS_NOT_YET_SUBMITTED),
                $this->getRepo()->getReference(IrhpPermitTypeEntity::class, $permitType->getId()),
                $this->getRepo()->getReference(LicenceEntity::class, $command->getLicence()),
                date('Y-m-d')
            );

            $this->getRepo('IrhpApplication')->save($irhpApplication);

            $this->result->addId('irhpApplication', $irhpApplication->getId());
            $this->result->addMessage('IRHP Application created successfully');
        }

        $stocks = $this->getRepo('IrhpPermitStock')->getAllValidStockByPermitType($permitType->getId());

        foreach ($stocks as $stock) {
            $window = $this->getRepo('IrhpPermitWindow')->fetchLastOpenWindowByStockId($stock->getId());

            $irhpPermitApplication = IrhpPermitApplicationEntity::createNew(
                $this->getRepo()->getReference(IrhpPermitWindowEntity::class, $window->getId()),
                $this->getRepo()->getReference(LicenceEntity::class, $command->getLicence()),
                $this->getRepo()->getReference(EcmtPermitApplicationEntity::class, isset($ecmtPermitApplication) ? $ecmtPermitApplication->getId() : null),
                $this->getRepo()->getReference(IrhpApplicationEntity::class, isset($irhpApplication) ? $irhpApplication->getId() : null)
            );

            $this->getRepo()->save($irhpPermitApplication);

            $this->result->addId('irhpPermitApplication' . $irhpPermitApplication->getId(), $irhpPermitApplication->getId());
            $this->result->addMessage('IRHP Permit Application created successfully');
        }

        return $this->result;
    }
}

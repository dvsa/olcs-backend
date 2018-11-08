<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Permits;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Transfer\Command\Permits\CreateEcmtPermitApplication as CreateEcmtPermitApplicationCmd;
use Dvsa\Olcs\Api\Domain\Command\Permits\CreateIrhpPermitApplication;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Dvsa\Olcs\Transfer\Command\CommandInterface;

/**
 * Create an ECMT Permit application
 *
 * @author Tonci Vidovic <tonci.vidovic@capgemini.com>
 */
final class CreateEcmtPermitApplication extends AbstractCommandHandler implements ToggleRequiredInterface
{
    use ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::BACKEND_ECMT];
    protected $repoServiceName = 'EcmtPermitApplication';

    protected $extraRepos = ['IrhpPermitWindow', 'IrhpPermitStock'];

    /**
     * Handle command
     *
     * @param CommandInterface $command command
     *
     * @return Result
     */
    public function handleCommand(CommandInterface $command)
    {
        /** @var CreateEcmtPermitApplicationCmd $ecmtPermitApplication */
        $ecmtPermitApplication = $this->createPermitApplicationObject($command);

        $this->getRepo()->save($ecmtPermitApplication);

        $this->result->addId('ecmtPermitApplication', $ecmtPermitApplication->getId());
        $this->result->addMessage('ECMT Permit Application created successfully');

        $stock = $this->getRepo('IrhpPermitStock')->getNextIrhpPermitStockByPermitType(
            EcmtPermitApplication::PERMIT_TYPE,
            new DateTime()
        );

        $window = $this->getRepo('IrhpPermitWindow')->fetchLastOpenWindowByStockId($stock->getId());

        $this->result->merge(
            $this->handleSideEffect(
                CreateIrhpPermitApplication::create(
                    [
                        'window' => $window->getId(),
                        'ecmtPermitApplication' => $ecmtPermitApplication->getId(),
                    ]
                )
            )
        );

        return $this->result;
    }

    /**
     * Create EcmtPermitApplication object
     *
     * @param CreateEcmtPermitApplicationCmd $command Command
     *
     * @return EcmtPermitApplication
     */
    private function createPermitApplicationObject(CreateEcmtPermitApplicationCmd $command): EcmtPermitApplication
    {
            return EcmtPermitApplication::createNew(
                $this->getRepo()->getRefdataReference(EcmtPermitApplication::STATUS_NOT_YET_SUBMITTED),
                $this->getRepo()->getRefdataReference(EcmtPermitApplication::PERMIT_TYPE),
                $this->getRepo()->getReference(LicenceEntity::class, $command->getLicence()),
                date('Y-m-d')
            );
    }
}

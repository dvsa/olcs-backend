<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Permits;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;

use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Entity\Permits\Sectors;
use Dvsa\Olcs\Transfer\Command\Permits\CreateEcmtPermitApplication as CreateFullPermitApplicationCmd;

use Dvsa\Olcs\Transfer\Command\CommandInterface;

/**
 * Create an ECMT Permit application
 *
 * @author Andy Newton
 */
final class CreateFullPermitApplication extends AbstractCommandHandler
{
    /**
     * @var string
     */
    protected $repoServiceName = 'EcmtPermitApplication';

    /**
     * Handle command
     *
     * @param CommandInterface $command command
     *
     * @return Result
     */
    public function handleCommand(CommandInterface $command)
    {
        /** @var CreateFullPermitApplicationCmd $ecmtPermitApplication */
        $ecmtPermitApplication = $this->createPermitApplicationObject($command);

        $this->getRepo()->save($ecmtPermitApplication);

        $result = new Result();

        $result->addId('ecmtPermitApplication', $ecmtPermitApplication->getId());
        $result->addMessage('EcmtPermitApplication created successfully');

        return $result;
    }

    /**
     * Create EcmtPermitApplication object
     *
     * @param CreateFullPermitApplicationCmd $command Command
     *
     * @return EcmtPermitApplication
     */
    private function createPermitApplicationObject(CreateFullPermitApplicationCmd $command): EcmtPermitApplication
    {
        return EcmtPermitApplication::createNewInternal(
            $this->getRepo()->getRefdataReference(EcmtPermitApplication::STATUS_NOT_YET_SUBMITTED),
            $this->getRepo()->getRefdataReference('lfs_ot'),
            $this->getRepo()->getRefdataReference(EcmtPermitApplication::PERMIT_TYPE),
            $this->getRepo()->getReference(LicenceEntity::class, $command->getLicence()),
            $this->getRepo()->getReference(Sectors::class, $command->getSectors()),
            $command->getCabotage(),
            $command->getDeclaration(),
            $command->getEmissions(),
            $command->getPermitsRequired(),
            $command->getTrips(),
            $command->getInternationalJourneys(),
            $command->getDateReceived()
        );
    }
}

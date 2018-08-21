<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Permits;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;

use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Entity\Permits\Sectors;
use Dvsa\Olcs\Api\Entity\ContactDetails\Country;
use Dvsa\Olcs\Transfer\Command\Permits\CreateFullPermitApplication as CreateFullPermitApplicationCmd;

use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Olcs\Logging\Log\Logger;

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

    protected $extraRepos = ['Country'];

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

        Logger::crit(print_r("KAHOONAS", true));
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


        foreach ($command->getCountryIds() as $countryId) {
            $countrys[] = $this->getRepo('Country')->getReference(Country::class, $countryId);
        }


        return EcmtPermitApplication::createNewInternal(
            $this->getRepo()->getRefdataReference(EcmtPermitApplication::STATUS_NOT_YET_SUBMITTED),
            $this->getRepo()->getRefdataReference('lfs_ot'),
            $this->getRepo()->getRefdataReference(EcmtPermitApplication::PERMIT_TYPE),
            $this->getRepo()->getReference(LicenceEntity::class, $command->getLicence()),
            $this->getRepo()->getReference(Sectors::class, $command->getSectors()),
            $countrys,
            $command->getCabotage(),
            $command->getDeclaration(),
            $command->getEmissions(),
            $command->getPermitsRequired(),
            $command->getTrips(),
            $this->getRepo()->getRefdataReference($command->getInternationalJourneys()),
            $command->getDateReceived()
        );
    }
}

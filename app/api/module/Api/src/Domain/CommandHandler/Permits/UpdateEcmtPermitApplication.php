<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Permits;

use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Entity\ContactDetails\Country;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Entity\Permits\Sectors;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Api\Domain\Command\Permits\UpdatePermitFee;

use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Transfer\Command\Permits\UpdateEcmtPermitApplication as UpdateEcmtPermitApplicationCmd;

/**
 * Update ECMT Permit Application
 *
 * @author Andy Newton
 */
final class UpdateEcmtPermitApplication extends AbstractCommandHandler implements ToggleRequiredInterface, TransactionedInterface
{
    use ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::BACKEND_PERMITS];
    protected $repoServiceName = 'EcmtPermitApplication';
    protected $extraRepos = ['Sectors', 'Country'];

    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();

        /**
         * @var $ecmtPermitApplication EcmtPermitApplication
         * @var $command UpdateEcmtPermitApplicationCmd
         */
        $countrys = new ArrayCollection();
        foreach ($command->getCountryIds() as $countryId) {
            $countrys->add($this->getRepo('Country')->getReference(Country::class, $countryId));
        }

        $ecmtPermitApplication = $this->getRepo()->fetchUsingId($command, Query::HYDRATE_OBJECT);

        $applicationDate = new DateTime($ecmtPermitApplication->getDateReceived());
        $commandDate = new DateTime($command->getDateReceived());

        if ((int)$ecmtPermitApplication->getPermitsRequired() !== (int)$command->getPermitsRequired()
            || $applicationDate->format('Y-m-d') !== $commandDate->format('Y-m-d')
        ) {
            $this->result->merge($this->handleSideEffect(UpdatePermitFee::create(
                [
                    'ecmtPermitApplicationId' => $ecmtPermitApplication->getId(),
                    'licenceId' => $ecmtPermitApplication->getLicence()->getId(),
                    'permitsRequired' => $command->getPermitsRequired(),
                    'permitType' => $ecmtPermitApplication::PERMIT_TYPE,
                    'receivedDate' => $command->getDateReceived()
                ]
            )));
        }

        $ecmtPermitApplication->update(
            $this->getRepo()->getRefdataReference($ecmtPermitApplication::PERMIT_TYPE),
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

        $this->getRepo()->save($ecmtPermitApplication);

        $result->addId('ecmtPermitApplication', $ecmtPermitApplication->getId());
        $result->addMessage('ECMT Permit Application updated');

        return $result;
    }
}

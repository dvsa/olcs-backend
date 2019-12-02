<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Permits;

use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\Olcs\Api\Domain\Command\Permits\CreateIrhpPermitApplication;
use Dvsa\Olcs\Api\Domain\Command\Permits\UpdatePermitFee;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\Exception\ForbiddenException;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\ContactDetails\Country;
use Dvsa\Olcs\Api\Entity\IrhpInterface;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitWindow;
use Dvsa\Olcs\Api\Entity\Permits\Sectors;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Transfer\Command\Permits\CreateEcmtPermitApplication as CreateEcmtPermitApplicationCmd;

/**
 * Create an ECMT Permit application
 *
 * @author Tonci Vidovic <tonci.vidovic@capgemini.com>
 */
final class CreateEcmtPermitApplication extends AbstractCommandHandler implements
    ToggleRequiredInterface,
    TransactionedInterface
{
    use ToggleAwareTrait;

    const LICENCE_INVALID_MSG = 'Licence ID %s with number %s is unable to make an ECMT application';

    protected $toggleConfig = [FeatureToggle::BACKEND_PERMITS];
    protected $repoServiceName = 'EcmtPermitApplication';

    protected $extraRepos = ['IrhpPermitWindow', 'Licence', 'Country'];

    /**
     * Handle command
     *
     * @param CommandInterface $command command
     *
     * @return Result
     * @throws ForbiddenException
     */
    public function handleCommand(CommandInterface $command)
    {
        /**
         * @var LicenceEntity $licence
         * @var CreateEcmtPermitApplicationCmd $command
         */
        $licence = $this->getRepo('Licence')->fetchById($command->getLicence());

        /** @var IrhpPermitWindow $window */
        $window = $this->getRepo('IrhpPermitWindow')->fetchLastOpenWindowByStockId(
            $command->getIrhpPermitStock()
        );

        if (!$licence->canMakeEcmtApplication($window->getIrhpPermitStock())) {
            $message = sprintf(self::LICENCE_INVALID_MSG, $licence->getId(), $licence->getLicNo());
            throw new ForbiddenException($message);
        }

        /** @var EcmtPermitApplication $ecmtPermitApplication */
        $ecmtPermitApplication =
            $command->getFromInternal()
                ? $this->createInternalPermitApplicationObject($command, $licence)
                : $this->createPermitApplicationObject($licence);


        $this->getRepo()->save($ecmtPermitApplication);

        if ($command->getFromInternal()) {
            $totalPermitsRequired = $command->getRequiredEuro5() + $command->getRequiredEuro6();
            if ($totalPermitsRequired > 0) {
                $this->result->merge($this->handleSideEffect(
                    UpdatePermitFee::create(
                        [
                            'ecmtPermitApplicationId' => $ecmtPermitApplication->getId(),
                            'licenceId' => $command->getLicence(),
                            'permitsRequired' => $totalPermitsRequired,
                            'permitType' => $ecmtPermitApplication::PERMIT_TYPE,
                            'receivedDate' => $ecmtPermitApplication->getDateReceived()
                        ]
                    )
                ));
            }
        }

        $this->result->addId('ecmtPermitApplication', $ecmtPermitApplication->getId());
        $this->result->addMessage('ECMT Permit Application created successfully');

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
     * @param LicenceEntity $licence licence
     *
     * @return EcmtPermitApplication
     */
    private function createPermitApplicationObject(LicenceEntity $licence): EcmtPermitApplication
    {
        return EcmtPermitApplication::createNew(
            $this->refData(IrhpInterface::SOURCE_SELFSERVE),
            $this->refData(IrhpInterface::STATUS_NOT_YET_SUBMITTED),
            $this->refData(EcmtPermitApplication::PERMIT_TYPE),
            $licence,
            date('Y-m-d')
        );
    }

    /**
     * Create EcmtPermitApplication object
     *
     * @param CreateEcmtPermitApplicationCmd $command Command
     * @param LicenceEntity $licence
     *
     * @return EcmtPermitApplication
     *
     */
    private function createInternalPermitApplicationObject(
        CreateEcmtPermitApplicationCmd $command,
        LicenceEntity $licence
    ): EcmtPermitApplication {
        $countrys = new ArrayCollection();
        foreach ($command->getCountryIds() as $countryId) {
            $countrys->add($this->getRepo('Country')->getReference(Country::class, $countryId));
        }

        return EcmtPermitApplication::createNewInternal(
            $this->getRepo()->getRefdataReference(IrhpInterface::SOURCE_INTERNAL),
            $this->getRepo()->getRefdataReference(IrhpInterface::STATUS_NOT_YET_SUBMITTED),
            $this->getRepo()->getRefdataReference(EcmtPermitApplication::PERMIT_TYPE),
            $licence,
            $command->getDateReceived(),
            $this->getRepo()->getReference(Sectors::class, $command->getSectors()),
            $countrys,
            $command->getCabotage(),
            $command->getRoadworthiness(),
            $command->getDeclaration(),
            $command->getEmissions(),
            $command->getRequiredEuro5(),
            $command->getRequiredEuro6(),
            $command->getTrips(),
            $this->getRepo()->getRefdataReference($command->getInternationalJourneys())
        );
    }
}

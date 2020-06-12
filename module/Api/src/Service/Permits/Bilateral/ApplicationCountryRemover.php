<?php

namespace Dvsa\Olcs\Api\Service\Permits\Bilateral;

use Dvsa\Olcs\Api\Domain\CommandHandlerManager;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication;
use Dvsa\Olcs\Api\Service\Cqrs\CommandCreator;
use Dvsa\Olcs\Transfer\Command\IrhpApplication\UpdateCountries;

class ApplicationCountryRemover
{
    /** @var CommandCreator */
    private $commandCreator;

    /** @var CommandHandlerManager */
    private $commandHandlerManager;

    /**
     * Create service instance
     *
     * @param CommandCreator $commandCreator
     * @param CommandHandlerManager $commandHandlerManager
     *
     * @return ApplicationCountryRemover
     */
    public function __construct(CommandCreator $commandCreator, CommandHandlerManager $commandHandlerManager)
    {
        $this->commandCreator = $commandCreator;
        $this->commandHandlerManager = $commandHandlerManager;
    }

    /**
     * Remove all traces of a country from an application
     *
     * @param IrhpPermitApplication $irhpPermitApplication
     */
    public function remove(IrhpPermitApplication $irhpPermitApplication)
    {
        $countryCodeForDeletion = $irhpPermitApplication->getIrhpPermitWindow()
            ->getIrhpPermitStock()
            ->getCountry()
            ->getId();

        $commandCountries = [];

        $irhpApplication = $irhpPermitApplication->getIrhpApplication();
        $countries = $irhpApplication->getCountrys();

        foreach ($countries as $country) {
            $countryCode = $country->getId();
            if ($countryCode != $countryCodeForDeletion) {
                $commandCountries[] = $countryCode;
            }
        }

        $commandParams = [
            'id' => $irhpApplication->getId(),
            'countries' => $commandCountries
        ];

        $command = $this->commandCreator->create(UpdateCountries::class, $commandParams);
        $this->commandHandlerManager->handleCommand($command, false);
    }
}

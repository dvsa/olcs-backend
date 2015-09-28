<?php

namespace Dvsa\Olcs\Api\Service\Document;

use Dvsa\Olcs\Api\Entity\Application\Application;
use Dvsa\Olcs\Api\Entity\Bus\BusReg;
use Dvsa\Olcs\Api\Entity\Cases\Cases;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation;
use Dvsa\Olcs\Api\Entity\Tm\TransportManager;

/**
 * Naming Service Aware Trait
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
trait NamingServiceAwareTrait
{
    /**
     * @var NamingService
     */
    private $documentNamingService;

    /**
     * @param NamingService $service
     */
    public function setNamingService(NamingService $service)
    {
        $this->documentNamingService = $service;
    }

    /**
     * @return NamingService
     */
    public function getNamingService()
    {
        return $this->documentNamingService;
    }

    /**
     * @param $command
     */
    public function determineEntityFromCommand($command)
    {
        if ($command->getCase() !== null) {
            return $this->getRepo()->getReference(Cases::class, $command->getCase());
        }

        if ($command->getApplication() !== null) {
            return $this->getRepo()->getReference(Application::class, $command->getApplication());
        }

        if ($command->getTransportManager() !== null) {
            return $this->getRepo()->getReference(TransportManager::class, $command->getTransportManager());
        }

        if ($command->getBusReg() !== null) {
            return $this->getRepo()->getReference(BusReg::class, $command->getBusReg());
        }

        if ($command->getLicence() !== null) {
            return $this->getRepo()->getReference(Licence::class, $command->getLicence());
        }

        if ($command->getIrfoOrganisation() !== null) {
            return $this->getRepo()->getReference(Organisation::class, $command->getIrfoOrganisation());
        }

        return null;
    }
}

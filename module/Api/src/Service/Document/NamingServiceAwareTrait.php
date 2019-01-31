<?php

namespace Dvsa\Olcs\Api\Service\Document;

use Dvsa\Olcs\Api\Entity\Application\Application;
use Dvsa\Olcs\Api\Entity\Bus\BusReg;
use Dvsa\Olcs\Api\Entity\Cases\Cases;
use Dvsa\Olcs\Api\Entity\Licence\ContinuationDetail;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation;
use Dvsa\Olcs\Api\Entity\Tm\TransportManager;
use Dvsa\Olcs\Api\Entity\Surrender;

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
    public function determineEntityFromCommand(array $data)
    {
        if (!empty($data['case'])) {
            return $this->getRepo()->getReference(Cases::class, $data['case']);
        }

        if (!empty($data['application'])) {
            return $this->getRepo()->getReference(Application::class, $data['application']);
        }

        if (!empty($data['transportManager'])) {
            return $this->getRepo()->getReference(TransportManager::class, $data['transportManager']);
        }

        if (!empty($data['busReg'])) {
            return $this->getRepo()->getReference(BusReg::class, $data['busReg']);
        }

        if (!empty($data['licence'])) {
            return $this->getRepo()->getReference(Licence::class, $data['licence']);
        }

        if (!empty($data['irfoOrganisation'])) {
            return $this->getRepo()->getReference(Organisation::class, $data['irfoOrganisation']);
        }

        if (!empty($data['continuationDetail'])) {
            return $this->getRepo()->getReference(ContinuationDetail::class, $data['continuationDetail']);
        }

        if (!empty($data['surrender'])) {
            return $this->getRepo()->getReference(Surrender::class, $data['surrender']);
        }

        return null;
    }
}

<?php

/**
 * Get a Transport Manager Application
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\TransportManagerApplication;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Domain\Repository\ApplicationOperatingCentre;
use Dvsa\Olcs\Api\Domain\Repository\LicenceOperatingCentre;
use Dvsa\Olcs\Api\Entity\Tm\TransportManagerApplication;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Get a Transport Manager Application
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class GetDetails extends AbstractQueryHandler
{
    protected $repoServiceName = 'TransportManagerApplication';
    /**
     * @var ApplicationOperatingCentre
     */
    protected $aocRepo;
    /**
     * @var LicenceOperatingCentre
     */
    protected $locRepo;
    /**
     * @var \Dvsa\Olcs\Api\Domain\Repository\PreviousConviction
     */
    protected $pcRepo;
    /**
     * @var \Dvsa\Olcs\Api\Domain\Repository\OtherLicences
     */
    protected $olRepo;
    /**
     * @var \Dvsa\Olcs\Api\Domain\Repository\TmEmployment
     */
    protected $tmeRepo;

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $this->aocRepo = $serviceLocator->getServiceLocator()->get('RepositoryServiceManager')
            ->get('ApplicationOperatingCentre');
        $this->locRepo = $serviceLocator->getServiceLocator()->get('RepositoryServiceManager')
            ->get('LicenceOperatingCentre');
        $this->pcRepo = $serviceLocator->getServiceLocator()->get('RepositoryServiceManager')
            ->get('PreviousConviction');
        $this->olRepo = $serviceLocator->getServiceLocator()->get('RepositoryServiceManager')
            ->get('OtherLicence');
        $this->tmeRepo = $serviceLocator->getServiceLocator()->get('RepositoryServiceManager')
            ->get('TmEmployment');

        return parent::createService($serviceLocator);
    }

    public function handleQuery(QueryInterface $query)
    {
        /* @var $tma TransportManagerApplication */
        $tma = $this->getRepo()->fetchDetails($query->getId());

        $this->loadApplicationOperatingCentres($tma->getApplication());
        $this->loadLicenceOperatingCentres($tma->getApplication()->getLicence());
        $this->loadTransportManagerPreviousConvictions($tma->getTransportManager());
        $this->loadTransportManagerOtherLicences($tma->getTransportManager());
        $this->loadTransportManagerEmployements($tma->getTransportManager());

        // null entities to repvent JSON recursion
        /* @var $otherLicence \Dvsa\Olcs\Api\Entity\OtherLicence\OtherLicence */
        foreach ($tma->getOtherLicences() as $otherLicence) {
            $otherLicence->setApplication(null);
            $otherLicence->setTransportManagerApplication(null);
        }

        return $tma;
    }

    protected function loadApplicationOperatingCentres(\Dvsa\Olcs\Api\Entity\Application\Application $application)
    {
        $aoc = $this->aocRepo->fetchByApplication($application->getId());
        // null entities to repvent JSON recursion
        foreach ($aoc as $oc) {
            $oc->setApplication(null);
        }
        $application->setOperatingCentres($aoc);
    }

    protected function loadLicenceOperatingCentres(\Dvsa\Olcs\Api\Entity\Licence\Licence $licence)
    {
        $loc = $this->locRepo->fetchByLicence($licence->getId());
        // null entities to repvent JSON recursion
        foreach ($loc as $oc) {
            $oc->setLicence(null);
        }
        $licence->setOperatingCentres($loc);
    }

    protected function loadTransportManagerPreviousConvictions(\Dvsa\Olcs\Api\Entity\Tm\TransportManager $tm)
    {
        $pcs = $this->pcRepo->fetchByTransportManager($tm->getId());
        // null entities to repvent JSON recursion
        /* @var $pc \Dvsa\Olcs\Api\Entity\Application\PreviousConviction */
        foreach ($pcs as $pc) {
            $pc->setApplication(null);
            $pc->setTransportManager(null);
        }
        $tm->setPreviousConvictions($pcs);
    }

    protected function loadTransportManagerOtherLicences(\Dvsa\Olcs\Api\Entity\Tm\TransportManager $tm)
    {
        $otherLicences = $this->olRepo->fetchByTransportManager($tm->getId());
        // null entities to repvent JSON recursion
        /* @var $otherLicence \Dvsa\Olcs\Api\Entity\OtherLicence\OtherLicence */
        foreach ($otherLicences as $otherLicence) {
            $otherLicence->setApplication(null);
            $otherLicence->setTransportManager(null);
            $otherLicence->setTransportManagerApplication(null);
            $otherLicence->setTransportManagerLicence(null);
            $otherLicence->setOperatingCentres([]);
        }
        $tm->setOtherLicences($otherLicences);
    }

    protected function loadTransportManagerEmployements(\Dvsa\Olcs\Api\Entity\Tm\TransportManager $tm)
    {
        $tmEmployments = $this->tmeRepo->fetchByTransportManager($tm->getId());
        // null entities to repvent JSON recursion
        /* @var $tmEmployment \Dvsa\Olcs\Api\Entity\Tm\TmEmployment */
        foreach ($tmEmployments as $tmEmployment) {
            $tmEmployment->setTransportManager(null);
        }
        $tm->setEmployments($tmEmployments);
    }
}

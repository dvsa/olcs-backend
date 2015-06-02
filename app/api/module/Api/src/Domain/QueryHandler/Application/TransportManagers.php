<?php

/**
 * Transport Managers Query Handler
 * Retreive the application and a list of transport manager applications/licences (including contact details)
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\Application;

use Zend\ServiceManager\ServiceLocatorInterface;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * Transport Managers Query Handler
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class TransportManagers extends AbstractQueryHandler
{
    protected $repoServiceName = 'Application';

    /**
     * @var \Dvsa\Olcs\Api\Domain\Repository\TransportManagerApplication
     */
    protected $tmaRepo;
    /**
     * @var \Dvsa\Olcs\Api\Domain\Repository\TransportManagerLicence
     */
    protected $tmlRepo;

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $this->tmaRepo = $serviceLocator->getServiceLocator()->get('RepositoryServiceManager')
            ->get('TransportManagerApplication');
        $this->tmlRepo = $serviceLocator->getServiceLocator()->get('RepositoryServiceManager')
            ->get('TransportManagerLicence');

        return parent::createService($serviceLocator);
    }

    public function handleQuery(QueryInterface $query)
    {
        /* @var $application \Dvsa\Olcs\Api\Entity\Application\Application */
        $application = $this->getRepo()->fetchUsingId($query);

        $tmaResults = $this->tmaRepo->fetchWithContactDetailsByApplication($application->getId());
        $tmlResults = $this->tmlRepo->fetchWithContactDetailsByLicence($application->getLicence()->getId());

        $data = $application->jsonSerialize();
        $data['transportManagers'] = $tmaResults;
        $data['licence']['tmLicences'] = $tmlResults;

        return $data;
    }
}

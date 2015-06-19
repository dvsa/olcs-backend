<?php

/**
 * Transport Managers Query Handler
 * Retreive the licence and a list of transport manager licence (including contact details)
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\Licence;

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
    protected $repoServiceName = 'Licence';

    /**
     * @var \Dvsa\Olcs\Api\Domain\Repository\TransportManagerLicence
     */
    protected $tmlRepo;

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $this->tmlRepo = $serviceLocator->getServiceLocator()->get('RepositoryServiceManager')
            ->get('TransportManagerLicence');

        return parent::createService($serviceLocator);
    }

    public function handleQuery(QueryInterface $query)
    {
        /* @var $licence \Dvsa\Olcs\Api\Entity\Licence\Licence */
        $licence = $this->getRepo()->fetchUsingId($query);
        $this->tmlRepo->fetchWithContactDetailsByLicence($licence->getId());

        return $this->result(
            $licence,
            [
                'tmLicences' => [
                    'transportManager' => [
                        'homeCd' => [
                            'person'
                        ]
                    ]
                ]
            ]
        );
    }
}

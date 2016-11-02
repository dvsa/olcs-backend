<?php

/**
 * Psv Vehicles
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\Licence;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Entity;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Dvsa\Olcs\Api\Domain\Service\PsvVehicles\PsvVehiclesQueryHelper;
use Doctrine\ORM\Query;

/**
 * Psv Vehicles
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class PsvVehicles extends AbstractQueryHandler
{
    protected $repoServiceName = 'Licence';
    protected $extraRepos = ['LicenceVehicle'];

    /**
     * @var PsvVehiclesQueryHelper
     */
    protected $helper;

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $this->helper = $serviceLocator->getServiceLocator()->get('PsvVehiclesQueryHelper');

        return parent::createService($serviceLocator);
    }

    public function handleQuery(QueryInterface $query)
    {
        /* @var $licence Entity\Licence\Licence */
        $licence = $this->getRepo()->fetchUsingId($query);
        $lvQuery = $this->getRepo('LicenceVehicle')->createPaginatedVehiclesDataForLicenceQueryPsv(
            $query,
            $licence->getId()
        );

        $flags = $this->helper->getCommonQueryFlags($licence, $query);

        $flags['canTransfer'] = !$licence->getOtherActiveLicences()->isEmpty();
        $flags['hasBreakdown'] = (int) $licence->getTotAuthVehicles() > 0;
        $flags['licenceVehicles'] = [
            'results' => $this->resultList(
                $this->getRepo('LicenceVehicle')->fetchPaginatedList($lvQuery, Query::HYDRATE_OBJECT),
                [
                    'vehicle',
                ]
            ),
            'count' => $this->getRepo('LicenceVehicle')->fetchPaginatedCount($lvQuery)
        ];
        $flags['activeVehicleCount'] = $licence->getActiveVehiclesCount();
        $flags['allVehicleCount'] = $licence->getLicenceVehicles()->count();

        return $this->result(
            $licence,
            [
                'organisation',
            ],
            $flags
        );
    }
}

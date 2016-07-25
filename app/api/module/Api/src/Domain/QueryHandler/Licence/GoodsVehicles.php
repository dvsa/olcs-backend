<?php

/**
 * Goods Vehicles
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\Licence;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Entity\User\Permission;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;

/**
 * Goods Vehicles
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class GoodsVehicles extends AbstractQueryHandler
{
    protected $repoServiceName = 'Licence';

    protected $extraRepos = ['LicenceVehicle'];

    public function handleQuery(QueryInterface $query)
    {
        /** @var LicenceEntity $licence */
        $licence = $this->getRepo()->fetchUsingId($query);

        $lvQuery = $this->getRepo('LicenceVehicle')->createPaginatedVehiclesDataForLicenceQuery(
            $query,
            $licence->getId()
        );

        return $this->result(
            $licence,
            [
                'organisation'
            ],
            [
                'canReprint' => true,
                'canTransfer' => $this->canTransfer($licence),
                'canExport' => $this->isGranted(Permission::SELFSERVE_USER),
                'canPrintVehicle' => $this->isGranted(Permission::INTERNAL_USER),
                'licenceVehicles' => [
                    'results' => $this->resultList(
                        $this->getRepo('LicenceVehicle')->fetchPaginatedList($lvQuery, Query::HYDRATE_OBJECT),
                        [
                            'vehicle',
                            'goodsDiscs',
                            'interimApplication'
                        ]
                    ),
                    'count' => $this->getRepo('LicenceVehicle')->fetchPaginatedCount($lvQuery)
                ],
                'spacesRemaining' => $licence->getRemainingSpaces(),
                'activeVehicleCount' => $licence->getActiveVehiclesCount(),
                'allVehicleCount' => $this->getRepo('LicenceVehicle')->fetchAllVehiclesCount($licence->getId())
            ]
        );
    }

    private function canTransfer(LicenceEntity $licence)
    {
        return $licence->getOtherActiveLicences()->count() > 0;
    }
}

<?php

/**
 * Goods Vehicles
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\Application;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Entity\User\Permission;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;
use Doctrine\ORM\Query;

/**
 * Goods Vehicles
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class GoodsVehicles extends AbstractQueryHandler
{
    protected $repoServiceName = 'Application';

    protected $extraRepos = ['LicenceVehicle'];

    protected $licenceVehicleMethod = 'createPaginatedVehiclesDataForApplicationQuery';

    public function handleQuery(QueryInterface $query)
    {
        /** @var ApplicationEntity $application */
        $application = $this->getRepo()->fetchUsingId($query);

        $lvQuery = $this->getRepo('LicenceVehicle')->{$this->licenceVehicleMethod}(
            $query,
            $application->getId(),
            $application->getLicence()->getId()
        );

        return $this->result(
            $application,
            [],
            [
                'canReprint' => $this->canReprint($application),
                'canTransfer' => false,
                'canExport' => false,
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
                'spacesRemaining' => $application->getRemainingSpaces(),
                'activeVehicleCount' => $application->getActiveVehiclesCount(),
                'allVehicleCount' => $application->getAllVehiclesCount()
            ]
        );
    }

    private function canReprint(ApplicationEntity $application)
    {
        return $application->getStatus()->getId() !== ApplicationEntity::APPLICATION_STATUS_NOT_SUBMITTED;
    }
}

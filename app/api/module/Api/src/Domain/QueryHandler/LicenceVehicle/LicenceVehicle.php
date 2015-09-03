<?php

/**
 * Licence Vehicle
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\LicenceVehicle;

use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareTrait;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Entity\User\Permission;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * Licence Vehicle
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class LicenceVehicle extends AbstractQueryHandler implements AuthAwareInterface
{
    use AuthAwareTrait;

    protected $repoServiceName = 'LicenceVehicle';

    protected $extraRepos = ['VehicleHistoryView'];

    public function handleQuery(QueryInterface $query)
    {
        $licenceVehicle = $this->getRepo()->fetchUsingId($query);

        $showHistory = $this->isGranted(Permission::INTERNAL_USER);

        $history = [];

        if ($showHistory) {
            $history = $this->getRepo('VehicleHistoryView')->fetchByVrm($licenceVehicle->getVehicle()->getVrm());
        }

        return $this->result(
            $licenceVehicle,
            [
                'vehicle' => [
                    'psvType',
                ],
                'goodsDiscs'
            ],
            [
                'showHistory' => $showHistory,
                'history' => $history
            ]
        );
    }
}

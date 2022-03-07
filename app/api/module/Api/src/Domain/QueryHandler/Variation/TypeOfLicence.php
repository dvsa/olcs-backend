<?php

/**
 * Type Of Licence
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\Variation;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Dvsa\Olcs\Api\Entity\User\Permission;
use Dvsa\Olcs\Api\Entity\Application\Application as Entity;
use Dvsa\Olcs\Api\Entity\System\RefData;

/**
 * Type Of Licence
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class TypeOfLicence extends AbstractQueryHandler
{
    protected $repoServiceName = 'Application';

    public function handleQuery(QueryInterface $query)
    {
        /** @var Entity $entity */
        $application = $this->getRepo()->fetchUsingId($query);
        $licence = $application->getLicence();

        $lockedVehicleTypeIds = [
            RefData::APP_VEHICLE_TYPE_MIXED,
            RefData::APP_VEHICLE_TYPE_LGV,
        ];
        $vehicleTypeId = $licence->getVehicleType()->getId();

        return $this->result(
            $application,
            [],
            [
                'canBecomeSpecialRestricted' => $licence->canBecomeSpecialRestricted(),
                'canBecomeStandardInternational' => $licence->canBecomeStandardInternational(),
                'canUpdateLicenceType' => $this->isGranted(Permission::CAN_UPDATE_LICENCE_LICENCE_TYPE, $licence) &&
                    !in_array($vehicleTypeId, $lockedVehicleTypeIds),
                'currentLicenceType' => $licence->getLicenceType()->getId()
            ]
        );
    }
}

<?php

/**
 * Cease Discs For Licence
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Repository\Query;

use Dvsa\Olcs\Api\Entity\Licence\LicenceVehicle;
use Dvsa\Olcs\Api\Entity\Vehicle\GoodsDisc;

/**
 * Cease Discs For Licence
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class CeaseDiscsForLicence extends AbstractRawQuery
{
    protected $templateMap = [
        'gd' => GoodsDisc::class,
        'lv' => LicenceVehicle::class
    ];

    protected $queryTemplate = 'UPDATE {gd}
      INNER JOIN {lv} ON {lv.id} = {gd.licenceVehicle}
      SET {gd.ceasedDate} = :ceasedDate, {gd.isInterim} = 0
      WHERE {lv.licence} = :licence AND {gd.ceasedDate} IS NULL';

    /**
     * {@inheritdoc}
     */
    protected function getParams()
    {
        return [
            'ceasedDate' => date('Y-m-d H:i:s')
        ];
    }
}

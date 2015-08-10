<?php

/**
 * Psv Licence Vehicle
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\LicenceVehicle;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * Psv Licence Vehicle
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class PsvLicenceVehicle extends AbstractQueryHandler
{
    protected $repoServiceName = 'LicenceVehicle';

    public function handleQuery(QueryInterface $query)
    {
        $licenceVehicle = $this->getRepo()->fetchUsingId($query);

        return $this->result(
            $licenceVehicle,
            ['vehicle']
        );
    }
}

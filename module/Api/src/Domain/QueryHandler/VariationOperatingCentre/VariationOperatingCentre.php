<?php

/**
 * Variation Operating Centre
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\VariationOperatingCentre;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\ApplicationOperatingCentre\ApplicationOperatingCentre;
use Dvsa\Olcs\Transfer\Query\LicenceOperatingCentre\LicenceOperatingCentre;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * Variation Operating Centre
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class VariationOperatingCentre extends AbstractQueryHandler
{
    public function handleQuery(QueryInterface $query)
    {
        $id = $query->getId();

        list($prefix, $id) = $this->splitTypeAndId($id);

        if ($prefix === 'L') {
            return $this->getQueryHandler()->handleQuery(
                LicenceOperatingCentre::create(
                    [
                        'id' => $id,
                        'isVariation' => true
                    ]
                )
            );
        } elseif ($prefix === 'A') {
            return $this->getQueryHandler()->handleQuery(ApplicationOperatingCentre::create(['id' => $id]));
        }

        throw new \Exception('Couldn\'t determine identity');
    }

    private function splitTypeAndId($ref)
    {
        $type = substr($ref, 0, 1);

        $id = (int)substr($ref, 1);

        return [$type, $id];
    }
}

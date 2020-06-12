<?php

/**
 * Retrieve Bilateral fees for each country
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\Fee;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\Fee\IrhpBilateralByCountry as BilateralFeesByCountryQry;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * Class IrhpBilateralByCountry
 * @todo following https://jira.dvsacloud.uk/browse/OLCS-26994 update or replace this once fees are in the database
 * @todo fees for SE, CH, FR are just placeholders for now
 */
class IrhpBilateralByCountry extends AbstractQueryHandler
{
    private $map = [
        'NO' => [
            'single' => [
                'total' => 8,
                'application' => 2,
                'grant' => 6
            ],
            'multi' => [
                'total' => 50,
                'application' => 5,
                'grant' => 45
            ],
            'cabotage' => [
                'total' => 8,
                'application' => 2,
                'grant' => 6
            ],
        ],
        'SE' => [
            'single' => [
                'total' => 8,
                'application' => 2,
                'grant' => 6
            ],
            'multi' => [
                'total' => 50,
                'application' => 5,
                'grant' => 45
            ],
            'cabotage' => [
                'total' => 8,
                'application' => 2,
                'grant' => 6
            ],
        ],
        'CH' => [
            'single' => [
                'total' => 8,
                'application' => 2,
                'grant' => 6
            ],
            'multi' => [
                'total' => 50,
                'application' => 5,
                'grant' => 45
            ],
            'cabotage' => [
                'total' => 8,
                'application' => 2,
                'grant' => 6
            ],
        ],
        'FR' => [
            'single' => [
                'total' => 8,
                'application' => 2,
                'grant' => 6
            ],
            'multi' => [
                'total' => 50,
                'application' => 5,
                'grant' => 45
            ],
            'cabotage' => [
                'total' => 8,
                'application' => 2,
                'grant' => 6
            ],
        ],
    ];

    public function handleQuery(QueryInterface $query)
    {
        /** @var BilateralFeesByCountryQry $query */
        $fees = isset($this->map[$query->getCountry()]) ? $this->map[$query->getCountry()] : [];
        $fees['hasFees'] = !empty($fees);

        return $fees;
    }
}

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
                'application' => 2,
                'grant' => 6
            ],
            'multi' => [
                'application' => 5,
                'grant' => 45
            ],
            'cabotage' => [
                'application' => 2,
                'grant' => 6
            ],
        ],
        'SE' => [
            'single' => [
                'application' => 2,
                'grant' => 6
            ],
            'multi' => [
                'application' => 5,
                'grant' => 45
            ],
            'cabotage' => [
                'application' => 2,
                'grant' => 6
            ],
        ],
        'CH' => [
            'single' => [
                'application' => 2,
                'grant' => 6
            ],
            'multi' => [
                'application' => 5,
                'grant' => 45
            ],
            'cabotage' => [
                'application' => 2,
                'grant' => 6
            ],
        ],
        'FR' => [
            'single' => [
                'application' => 2,
                'grant' => 6
            ],
            'multi' => [
                'application' => 5,
                'grant' => 45
            ],
            'cabotage' => [
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

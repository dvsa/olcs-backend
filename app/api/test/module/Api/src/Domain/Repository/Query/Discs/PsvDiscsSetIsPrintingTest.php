<?php

/**
 * Psv Discs Set Is Printing Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\Repository\Query\Discs;

use Dvsa\Olcs\Api\Domain\Repository\Query\Discs\PsvDiscsSetIsPrinting;
use Dvsa\Olcs\Api\Entity\Licence\PsvDisc;
use Dvsa\OlcsTest\Api\Domain\Repository\Query\AbstractDbUpdateTestCase;
use Doctrine\DBAL\Connection;
use Mockery as m;

/**
 * Psv Discs Set Is Printing Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class PsvDiscsSetIsPrintingTest extends AbstractDbUpdateTestCase
{
    protected $tableNameMap = [
        PsvDisc::class => 'psv_disc'
    ];

    protected $columnNameMap = [
        PsvDisc::class => [
            'isPrinting' => [
                'column' => 'is_printing'
            ],
            'id' => [
                'column' => 'id'
            ],
        ],
    ];

    public function paramProvider()
    {
        return [
            [
                ['isPrinting' => 1, 'ids' => [1,2]],
                ['isPrinting' => \PDO::PARAM_INT, 'ids' => Connection::PARAM_INT_ARRAY],
                ['isPrinting' => 1, 'ids' => [1,2]],
                ['isPrinting' => \PDO::PARAM_INT, 'ids' => Connection::PARAM_INT_ARRAY]
            ]
        ];
    }

    protected function getSut()
    {
        return new PsvDiscsSetIsPrinting();
    }

    protected function getExpectedQuery()
    {
        return 'UPDATE psv_disc pd
      SET pd.is_printing = :isPrinting
      WHERE pd.id IN (:ids)';
    }
}

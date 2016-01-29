<?php

/**
 * Goods Discs Set Is Printing Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\Repository\Query\Discs;

use Dvsa\Olcs\Api\Domain\Repository\Query\Discs\GoodsDiscsSetIsPrinting;
use Dvsa\Olcs\Api\Entity\Vehicle\GoodsDisc;
use Dvsa\OlcsTest\Api\Domain\Repository\Query\AbstractDbUpdateTestCase;
use Doctrine\DBAL\Connection;
use Mockery as m;

/**
 * Goods Discs Set Is Printing Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class GoodsDiscsSetIsPrintingTest extends AbstractDbUpdateTestCase
{
    protected $tableNameMap = [
        GoodsDisc::class => 'goods_disc'
    ];

    protected $columnNameMap = [
        GoodsDisc::class => [
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
        return new GoodsDiscsSetIsPrinting();
    }

    protected function getExpectedQuery()
    {
        return 'UPDATE goods_disc gd
      SET gd.is_printing = :isPrinting
      WHERE gd.id IN (:ids)';
    }
}

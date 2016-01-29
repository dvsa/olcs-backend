<?php

/**
 * Goods Discs Set Is Printing Off and Disc No Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\Repository\Query\Discs;

use Dvsa\Olcs\Api\Domain\Repository\Query\Discs\GoodsDiscsSetIsPrintingOffAndDiscNo;
use Dvsa\Olcs\Api\Entity\Vehicle\GoodsDisc;
use Dvsa\OlcsTest\Api\Domain\Repository\Query\AbstractDbUpdateTestCase;
use Doctrine\DBAL\Connection;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;

/**
 * Goods Discs Set Is Printing Off and Disc No Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class GoodsDiscsSetIsPrintingOffAndDiscNoTest extends AbstractDbUpdateTestCase
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
            'discNo' => [
                'column' => 'disc_no'
            ],
            'issuedDate' => [
                'column' => 'issued_date'
            ],
        ],
    ];

    public function paramProvider()
    {
        $today = new DateTime();
        return [
            [
                ['ids' => [1,2], 'startNumber' => 1],
                ['ids' => Connection::PARAM_INT_ARRAY, 'startNumber' => \PDO::PARAM_INT],
                [
                    'issuedDate' => $today->format('Y-m-d H:i:s'),
                    'ids' => [1,2],
                    'startNumber' => 1
                ],
                [
                    'issuedDate' => \PDO::PARAM_STR,
                    'ids' => Connection::PARAM_INT_ARRAY,
                    'startNumber' => \PDO::PARAM_INT]
            ]
        ];
    }

    protected function getSut()
    {
        return new GoodsDiscsSetIsPrintingOffAndDiscNo();
    }

    protected function getExpectedQuery()
    {
        return 'UPDATE goods_disc gd,
       (SELECT @n := :startNumber - 1) m
          SET gd.is_printing = 0, gd.disc_no = @n := @n + 1, gd.issued_date = :issuedDate
          WHERE gd.id IN (:ids)';
    }
}

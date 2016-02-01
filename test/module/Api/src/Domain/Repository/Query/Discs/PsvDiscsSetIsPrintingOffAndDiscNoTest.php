<?php

/**
 * PSV Discs Set Is Printing Off and Disc No Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\Repository\Query\Discs;

use Dvsa\Olcs\Api\Domain\Repository\Query\Discs\PsvDiscsSetIsPrintingOffAndDiscNo;
use Dvsa\Olcs\Api\Entity\Licence\PsvDisc;
use Dvsa\OlcsTest\Api\Domain\Repository\Query\AbstractDbQueryTestCase;
use Doctrine\DBAL\Connection;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;

/**
 * PSV Discs Set Is Printing Off and Disc No Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class PsvDiscsSetIsPrintingOffAndDiscNoTest extends AbstractDbQueryTestCase
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
        return new PsvDiscsSetIsPrintingOffAndDiscNo();
    }

    protected function getExpectedQuery()
    {
        return 'UPDATE psv_disc pd,
       (SELECT @n := :startNumber - 1) m
          SET pd.is_printing = 0, pd.disc_no = @n := @n + 1, pd.issued_date = :issuedDate
          WHERE pd.id IN (:ids)';
    }
}

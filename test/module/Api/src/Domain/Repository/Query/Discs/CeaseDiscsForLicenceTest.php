<?php

/**
 * Cease Discs For Licence Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\Repository\Query\Discs;

use Dvsa\Olcs\Api\Domain\Repository\Query\Discs\CeaseDiscsForLicence;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Dvsa\Olcs\Api\Entity\Licence\PsvDisc;
use Dvsa\OlcsTest\Api\Domain\Repository\Query\AbstractDbQueryTestCase;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\ServiceManager\ServiceManager;

/**
 * Cease Discs For Licence Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class CeaseDiscsForLicenceTest extends AbstractDbQueryTestCase
{
    protected $tableNameMap = [
        PsvDisc::class => 'psv_disc'
    ];

    protected $columnNameMap = [
        PsvDisc::class => [
            'licence' => [
                'isAssociation' => true,
                'column' => 'licence_id'
            ],
            'ceasedDate' => [
                'column' => 'ceased_date'
            ],
        ],
    ];

    public function paramProvider()
    {
        $today = new DateTime();

        return [
            [
                [],
                [
                    'ceasedDate' => $today->format('Y-m-d H:i:s')
                ]
            ]
        ];
    }

    protected function getSut()
    {
        return new CeaseDiscsForLicence();
    }

    protected function getExpectedQuery()
    {
        return 'UPDATE psv_disc pd
      SET pd.ceased_date = :ceasedDate
      WHERE pd.licence_id = :licence AND pd.ceased_date IS NULL';
    }
}

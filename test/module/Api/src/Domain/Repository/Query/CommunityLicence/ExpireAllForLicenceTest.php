<?php

/**
 * Expire All For Licence Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\Repository\Query\CommunityLicence;

use Dvsa\Olcs\Api\Domain\Repository\Query\CommunityLicence\ExpireAllForLicence;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Dvsa\Olcs\Api\Entity\CommunityLic\CommunityLic;
use Dvsa\OlcsTest\Api\Domain\Repository\Query\AbstractDbQueryTestCase;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Zend\ServiceManager\ServiceManager;

/**
 * Expire All For Licence Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ExpireAllForLicenceTest extends AbstractDbQueryTestCase
{
    protected $tableNameMap = [
        CommunityLic::class => 'com_lic'
    ];

    protected $columnNameMap = [
        CommunityLic::class => [
            'status' => [
                'isAssociation' => true,
                'column' => 'status_id'
            ],
            'expiredDate' => [
                'column' => 'expired_date'
            ],
            'licence' => [
                'isAssociation' => true,
                'column' => 'licence_id'
            ]
        ]
    ];

    public function paramProvider()
    {
        $today = new DateTime();

        return [
            [
                [],
                [
                    'status' => CommunityLic::STATUS_EXPIRED,
                    'expiredDate' => $today->format('Y-m-d H:i:s')
                ]
            ]
        ];
    }

    protected function getSut()
    {
        return new ExpireAllForLicence();
    }

    protected function getExpectedQuery()
    {
        return 'UPDATE com_lic cl
      SET cl.status_id = :status, cl.expired_date = :expiredDate
      WHERE cl.expired_date IS NULL AND cl.licence_id = :licence';
    }
}

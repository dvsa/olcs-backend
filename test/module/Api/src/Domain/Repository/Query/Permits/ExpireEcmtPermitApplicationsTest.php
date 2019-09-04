<?php

namespace Dvsa\OlcsTest\Api\Domain\Repository\Query\Permits;

use Doctrine\DBAL\Connection;
use Dvsa\Olcs\Api\Domain\Repository\Query\Permits\ExpireEcmtPermitApplications as ExpireEcmtPermitApplicationsQry;
use Dvsa\Olcs\Api\Entity\IrhpInterface;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermit;
use Dvsa\OlcsTest\Api\Domain\Repository\Query\AbstractDbQueryTestCase;

/**
 * ExpireEcmtPermitApplications test
 */
class ExpireEcmtPermitApplicationsTest extends AbstractDbQueryTestCase
{
    protected $tableNameMap = [
        EcmtPermitApplication::class => 'epa_table',
        IrhpPermitApplication::class => 'ipa_table',
        IrhpPermit::class => 'ip_table',
    ];

    protected $columnNameMap = [
        EcmtPermitApplication::class => [
            'id' => [
                'column' => 'id'
            ],
            'status' => [
                'column' => 'status'
            ],
            'lastModifiedOn' => [
                'column' => 'last_modified_on'
            ],
            'lastModifiedBy' => [
                'column' => 'last_modified_by'
            ],
            'version' => [
                'column' => 'version'
            ],
            'expiryDate' => [
                'column' => 'expiry_date'
            ],
        ],
        IrhpPermitApplication::class => [
            'id' => [
                'column' => 'id'
            ],
            'ecmtPermitApplication' => [
                'column' => 'ecmt_permit_application_id'
            ],
        ],
        IrhpPermit::class => [
            'id' => [
                'column' => 'id'
            ],
            'irhpPermitApplication' => [
                'column' => 'irhp_permit_application_id'
            ],
            'status' => [
                'column' => 'status'
            ],
        ],
    ];

    public function paramProvider()
    {
        return [
            [
                [],
                [],
                [
                    'expiredStatus' => IrhpInterface::STATUS_EXPIRED,
                    'validStatus' => IrhpInterface::STATUS_VALID,
                    'permitValidStatuses' => IrhpPermit::$validStatuses,
                    'currentUserId' => 1,
                ],
                [
                    'permitValidStatuses' => Connection::PARAM_STR_ARRAY,
                ]
            ]
        ];
    }

    protected function getSut()
    {
        return new ExpireEcmtPermitApplicationsQry();
    }

    protected function getExpectedQuery()
    {
        return 'UPDATE epa_table epa '
            . 'SET epa.status = :expiredStatus, '
            . 'epa.expiry_date = NOW(), '
            . 'epa.last_modified_on = NOW(), '
            . 'epa.last_modified_by = :currentUserId, '
            . 'epa.version = epa.version + 1 '
            . 'WHERE epa.status = :validStatus '
            . 'AND epa.id NOT IN ( '
                . 'SELECT ipa.ecmt_permit_application_id '
                . 'FROM ipa_table ipa '
                . 'INNER JOIN ip_table ip '
                    . 'ON ip.irhp_permit_application_id = ipa.id '
                        . 'AND ip.status IN (:permitValidStatuses) '
                . 'WHERE ipa.ecmt_permit_application_id IS NOT NULL '
            . ')';
    }
}

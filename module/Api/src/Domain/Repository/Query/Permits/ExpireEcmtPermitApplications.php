<?php

namespace Dvsa\Olcs\Api\Domain\Repository\Query\Permits;

use Doctrine\DBAL\Connection;
use Dvsa\Olcs\Api\Domain\Repository\Query\AbstractRawQuery;
use Dvsa\Olcs\Api\Entity\IrhpInterface;
use Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermit;

/**
 * Expire ECMT applications
 */
class ExpireEcmtPermitApplications extends AbstractRawQuery
{
    /** @var array $templateMap */
    protected $templateMap = [
        'epa' => EcmtPermitApplication::class,
        'ipa' => IrhpPermitApplication::class,
        'ip' => IrhpPermit::class,
    ];

    /** @var string $queryTemplate */
    protected $queryTemplate = 'UPDATE {epa}
      SET {epa.status} = :expiredStatus,
        {epa.expiryDate} = NOW(),
        {epa.lastModifiedOn} = NOW(),
        {epa.lastModifiedBy} = :currentUserId,
        {epa.version} = {epa.version} + 1
      WHERE {epa.status} = :validStatus
        AND {epa.id} NOT IN (
          SELECT {ipa.ecmtPermitApplication}
          FROM {ipa}
          INNER JOIN {ip} ON {ip.irhpPermitApplication} = {ipa.id} AND {ip.status} IN (:permitValidStatuses)
          WHERE {ipa.ecmtPermitApplication} IS NOT NULL
        )';

    /**
     * {@inheritdoc}
     */
    protected function getParams()
    {
        return [
            'expiredStatus' => IrhpInterface::STATUS_EXPIRED,
            'validStatus' => IrhpInterface::STATUS_VALID,
            'permitValidStatuses' => IrhpPermit::$validStatuses,
        ];
    }

    /**
     * {@inheritdoc}
     */
    protected function getParamTypes()
    {
        return [
            'permitValidStatuses' => Connection::PARAM_STR_ARRAY,
        ];
    }
}

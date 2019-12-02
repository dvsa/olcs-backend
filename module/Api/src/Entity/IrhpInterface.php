<?php

namespace Dvsa\Olcs\Api\Entity;

/**
 * IRHP Interface
 */
interface IrhpInterface
{
    const STATUS_CANCELLED = 'permit_app_cancelled';
    const STATUS_NOT_YET_SUBMITTED = 'permit_app_nys';
    const STATUS_UNDER_CONSIDERATION = 'permit_app_uc';
    const STATUS_WITHDRAWN = 'permit_app_withdrawn';
    const STATUS_AWAITING_FEE = 'permit_app_awaiting';
    const STATUS_FEE_PAID = 'permit_app_fee_paid';
    const STATUS_UNSUCCESSFUL = 'permit_app_unsuccessful';
    const STATUS_ISSUED = 'permit_app_issued';
    const STATUS_ISSUING = 'permit_app_issuing';
    const STATUS_VALID = 'permit_app_valid';
    const STATUS_EXPIRED = 'permit_app_expired';

    const ACTIVE_STATUSES = [
        self::STATUS_NOT_YET_SUBMITTED,
        self::STATUS_UNDER_CONSIDERATION,
        self::STATUS_AWAITING_FEE,
        self::STATUS_FEE_PAID,
        self::STATUS_ISSUING,
    ];

    const PRE_GRANT_STATUSES = [
        self::STATUS_UNDER_CONSIDERATION
    ];

    const SOURCE_SELFSERVE = 'app_source_selfserve';
    const SOURCE_INTERNAL = 'app_source_internal';

    public function getId();
}

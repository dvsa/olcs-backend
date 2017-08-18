<?php

namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Domain\Exception\NotFoundException;
use Dvsa\Olcs\Api\Entity\System\SystemParameter as Entity;

/**
 * System Parameter
 */
class SystemParameter extends AbstractRepository
{
    protected $entity = Entity::class;

    /**
     * Fetch a system parameter value, return null if not found
     *
     * @param string $key The system parameter name, see SystemParameter entity constants
     *
     * @return mixed|null
     */
    public function fetchValue($key)
    {
        try {
            return $this->fetchById($key)->getParamValue();
        } catch (NotFoundException $ex) {
            return null;
        }
    }

    /**
     * Get Disable card payment setting
     *
     * @return bool
     */
    public function getDisableSelfServeCardPayments()
    {
        return (bool) $this->fetchValue(Entity::DISABLED_SELFSERVE_CARD_PAYMENTS);
    }

    /**
     * Get Disable Gds Verify Signatures
     *
     * @return bool Return true if disabled
     */
    public function getDisableGdsVerifySignatures()
    {
        return (bool) $this->fetchValue(Entity::DISABLE_GDS_VERIFY_SIGNATURES);
    }

    /**
     * Get Disable Digital Continuations
     *
     * @return bool Return true if disabled
     */
    public function getDisabledDigitalContinuations()
    {
        return (bool) $this->fetchValue(Entity::DISABLE_DIGITAL_CONTINUATIONS);
    }

    /**
     * Get Disable Data Retention Records
     *
     * @return bool Return true if disabled
     */
    public function getDisableDataRetentionRecords()
    {
        return (bool) $this->fetchValue(Entity::DISABLE_DATA_RETENTION_RECORDS);
    }
}

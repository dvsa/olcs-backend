<?php

/**
 * Read Audit Repository Interface
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Repository\ReadAudit;

/**
 * Read Audit Repository Interface
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
interface ReadAuditRepositoryInterface
{
    /**
     * Remove older records
     *
     * @param $oldestDate
     * @return int
     */
    public function deleteOlderThan($oldestDate);

    /**
     * Fetch one record by userId, entityId and date
     *
     * @param $userId
     * @param $entityId
     * @param $date
     * @return mixed
     */
    public function fetchOne($userId, $entityId, $date);
}

<?php

namespace Dvsa\Olcs\Api\Domain\Repository\Query\Continuations;

use Dvsa\Olcs\Api\Domain\Repository\Query\AbstractRawQuery;
use Dvsa\Olcs\Api\Entity\Licence\ContinuationDetail;
use Dvsa\Olcs\Api\Domain\Exception\RuntimeException;

/**
 * Create continuation details
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class CreateContinuationDetails extends AbstractRawQuery
{
    protected $templateMap = [
        'cd' => ContinuationDetail::class
    ];

    protected $queryTemplate = 'INSERT INTO {cd}
        ({cd.licence}, {cd.received}, {cd.status}, {cd.continuation}, {cd.createdOn}, {cd.createdBy}) VALUES ';

    /**
     * Create continuation details rows
     *
     * @param array $licenceIds
     * @param bool $received
     * @param string $status
     * @param int $continuationId
     *
     * @return int Number of rows inserted
     *
     * @throws RuntimeException
     */
    public function executeInsert($licenceIds, $received, $status, $continuationId)
    {
        $query = $this->buildQueryFromTemplate($this->getQueryTemplate(), false);

        for ($i = 0; $i < count($licenceIds); $i++) {
            if ($i !== 0) {
                $query .= ', ';
            }
            $query .= sprintf(
                '(%s, %s, %s, %s, NOW(), %s)',
                $this->connection->quote($licenceIds[$i]),
                $this->connection->quote($received ? 1 : 0),
                $this->connection->quote($status),
                $this->connection->quote($continuationId),
                $this->getCurrentUser()->getId()
            );
        }

        try {
            return $this->connection->executeUpdate($query);
        } catch (\Exception $ex) {
            throw new RuntimeException('An unexpected error occurred while running query: ' . get_class($this));
        }
    }
}

<?php

namespace Dvsa\Olcs\Api\Domain\Repository\Query\Discs;

use Dvsa\Olcs\Api\Domain\Repository\Query\AbstractRawQuery;
use Dvsa\Olcs\Api\Entity\Licence\PsvDisc;
use Dvsa\Olcs\Api\Domain\Exception\RuntimeException;

/**
 * CreatePsvDiscs
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class CreatePsvDiscs extends AbstractRawQuery
{
    protected $templateMap = [
        'pd' => PsvDisc::class
    ];

    protected $queryTemplate = 'INSERT INTO {pd} ({pd.licence}, {pd.isCopy}, {pd.createdOn}) VALUES ';

    /**
     * Create PSV disc rows
     *
     * @param int  $licenceId The licence
     * @param int  $howMany   How many discs to create
     * @param bool $isCopy
     *
     * @return int Number of rows inserted
     *
     * @throws RuntimeException
     */
    public function executeInsert($licenceId, $howMany, $isCopy)
    {
        $query = $this->buildQueryFromTemplate($this->getQueryTemplate(), false);
        for ($i = 0; $i < $howMany; $i++) {
            if ($i !== 0) {
                $query .= ', ';
            }
            $query .= sprintf(
                '(%s, %s, NOW())',
                $this->connection->quote($licenceId),
                $this->connection->quote($isCopy ? 1 : 0)
            );
        }

        try {
            return $this->connection->executeUpdate($query);
        } catch (\Exception $ex) {
            throw new RuntimeException('An unexpected error occurred while running query: ' . get_class($this));
        }
    }
}

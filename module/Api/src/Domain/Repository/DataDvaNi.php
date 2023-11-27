<?php

namespace Dvsa\Olcs\Api\Domain\Repository;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Result;

/**
 * Contains methods to get data from DB to export for data for Northern Ireland
 */
class DataDvaNi
{
    private Connection $conn;

    /**
     * DataDvaNi constructor.
     *
     * @param Connection $conn Database connection
     */
    public function __construct(Connection $conn)
    {
        $this->conn = $conn;
    }

    /**
     * Close database connection
     *
     * @return void
     */
    public function __destruct()
    {
        $this->conn->close();
    }

    /**
     * Fetch operator licences in specified areas
     */
    public function fetchNiOperatorLicences(): Result
    {

        //  query data
        $stmt = $this->conn->prepare(
            'SELECT * FROM data_dva_ni_operator_licence_view'
        );

        return $stmt->executeQuery();
    }
}

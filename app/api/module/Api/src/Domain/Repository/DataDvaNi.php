<?php

namespace Dvsa\Olcs\Api\Domain\Repository;

use Doctrine\DBAL\Connection;

/**
 * Contains methods to get data from DB to export for data for Northern Ireland
 */
class DataDvaNi
{
    /**
     * @var Connection
     */
    private $conn;

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
        if ($this->conn !== null) {
            $this->conn->close();
        }
    }

    /**
     * Fetch operator licences in specified areas
     *
     * @return \Doctrine\DBAL\Driver\Statement
     */
    public function fetchNiOperatorLicences()
    {

        //  query data
        $stmt = $this->conn->prepare(
            'SELECT * FROM data_dva_ni_operator_licence_view'
        );

        $stmt->execute();

        return $stmt;
    }
}

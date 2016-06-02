<?php

namespace Dvsa\Olcs\Api\Domain\Repository;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;

/**
 * Contains methods to get data from DB to export for data.gov.uk
 *
 * @author Dmitry Golubev <dmitrij.golubev@valtech.co.uk>
 */
class DataGovUk
{
    /** @var Connection */
    private $conn;

    public function __construct(Connection $conn)
    {
        $this->conn = $conn;
    }

    public function fetchOperatorLicences()
    {
        //  query data
        $stmt = $this->conn->query('SELECT * FROM data_gov_uk_operator_licence_view');

        //  close db connection
        $this->conn->close();

        return $stmt;
    }
}

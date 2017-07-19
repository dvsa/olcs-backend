<?php

namespace Dvsa\Olcs\Api\Domain\Repository;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use PDO;

/**
 * Contains methods to get data from DB to export for data.gov.uk
 *
 * @author Dmitry Golubev <dmitrij.golubev@valtech.co.uk>
 */
class DataGovUk
{
    /**
     * @var Connection
     */
    private $conn;

    /**
     * DataGovUk constructor.
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
     * Fetch PSV Operator list
     *
     * @return \Doctrine\DBAL\Driver\Statement
     */
    public function fetchPsvOperatorList()
    {
        $stmt = $this->conn->prepare(
            'SELECT * FROM data_gov_uk_psv_operator_list'
        );

        $stmt->execute();

        return $stmt;
    }

    /**
     * Fetch operator licences in specified areas
     *
     * @param array $areaNames Area names
     *
     * @return \Doctrine\DBAL\Driver\Statement
     */
    public function fetchOperatorLicences(array $areaNames)
    {
        $inStmt = implode(', ', array_fill(0, count($areaNames), '?'));

        //  query data
        $stmt = $this->conn->prepare(
            'SELECT * FROM data_gov_uk_operator_licence_view WHERE `GeographicRegion` IN (' . $inStmt . ')'
        );

        foreach ($areaNames as $idx => $name) {
            $stmt->bindValue($idx + 1, $name, PDO::PARAM_STR);
        }

        $stmt->execute();

        return $stmt;
    }

    /**
     * Fetch bus registered only
     *
     * @param array $areaCodes Area codes
     *
     * @return \Doctrine\DBAL\Driver\Statement
     */
    public function fetchBusRegisteredOnly(array $areaCodes)
    {
        return $this->fetchBusReg('data_gov_uk_bus_registered_only_view', $areaCodes);
    }

    /**
     * Fetch bus variations
     *
     * @param array $areaCodes area codes
     *
     * @return \Doctrine\DBAL\Driver\Statement
     */
    public function fetchBusVariation(array $areaCodes)
    {
        return $this->fetchBusReg('data_gov_uk_bus_variation_view', $areaCodes);
    }

    /**
     * Fetch bus registrations
     *
     * @param string $view      Database View
     * @param array  $areaCodes area codes
     *
     * @return \Doctrine\DBAL\Driver\Statement
     */
    private function fetchBusReg($view, array $areaCodes)
    {
        $inStmt = implode(', ', array_fill(0, count($areaCodes), '?'));

        $stmt = $this->conn->prepare(
            'SELECT * FROM ' . $view . ' WHERE `Current Traffic Area` IN (' . $inStmt . ')'
        );

        foreach ($areaCodes as $idx => $code) {
            $stmt->bindValue($idx + 1, $code, PDO::PARAM_STR);
        }

        $stmt->execute();

        return $stmt;
    }
}

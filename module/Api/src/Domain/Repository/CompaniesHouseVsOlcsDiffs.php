<?php

namespace Dvsa\Olcs\Api\Domain\Repository;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;

/**
 * Contains methods to get difference between company house and olcs data from DB
 *
 * @author Dmitry Golubev <dmitrij.golubev@valtech.co.uk>
 */
class CompaniesHouseVsOlcsDiffs
{
    /** @var Connection */
    private $conn;

    /**
     * constructor.
     *
     * @param Connection $conn DB connection
     */
    public function __construct(Connection $conn)
    {
        $this->conn = $conn;
    }

    /**
     * Desctructor
     */
    public function __destruct()
    {
        if ($this->conn !== null) {
            $this->conn->close();
        }
    }

    /**
     * Fetch ogranisation officer (people) differences from db
     *
     * @return \Doctrine\DBAL\Driver\Statement
     */
    public function fetchOfficerDiffs()
    {
        return $this->conn->query('CALL sp_ch_vs_olcs_diff_organisation_officer');
    }

    /**
     * Fetch ogranisation address differences from db
     *
     * @return \Doctrine\DBAL\Driver\Statement
     */
    public function fetchAddressDiffs()
    {
        return $this->conn->query('CALL sp_ch_vs_olcs_diff_organisation_address');
    }

    /**
     * Fetch ogranisation name differences from db
     *
     * @return \Doctrine\DBAL\Driver\Statement
     */
    public function fetchNameDiffs()
    {
        return $this->conn->query('CALL sp_ch_vs_olcs_diff_organisation_name');
    }

    /**
     * Fetch ogranisation with not active status in company house
     *
     * @return \Doctrine\DBAL\Driver\Statement
     */
    public function fetchWithNotActiveStatus()
    {
        return $this->conn->query('SELECT * FROM vw_ch_vs_olcs_diff_organisation_not_active');
    }
}

<?php

namespace Dvsa\Olcs\Cli\Domain\QueryHandler\DataRetention;

use Doctrine\ORM\EntityManager;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Cli\Domain\Query\DataRetention\TableChecks as TableChecksQuery;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use PDO;
use RuntimeException;
use Zend\ServiceManager\ServiceLocatorInterface;

final class TableChecks extends AbstractQueryHandler
{
    use EscapeMysqlIdentifierTrait;

    /** @var string */
    private $databaseName;

    /** @var PDO */
    private $connection;

    /**
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return AbstractQueryHandler
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var EntityManager $entityManager */
        $entityManager = $serviceLocator->getServiceLocator()->get('DoctrineOrmEntityManager');
        $this->databaseName = $entityManager->getConnection()->getParams()['dbname'];
        $this->connection = $entityManager->getConnection()->getWrappedConnection();
        return parent::createService($serviceLocator);
    }

    /**
     * @param QueryInterface $query
     * @return array
     */
    public function handleQuery(QueryInterface $query)
    {
        /** @var TableChecksQuery $query */
        $tableStatement = $this->connection->prepare(
            "SELECT TABLE_NAME
             FROM information_schema.tables
             WHERE TABLE_SCHEMA = :schema
             AND TABLE_NAME NOT LIKE '%_hist'
             AND TABLE_NAME NOT LIKE 'DR_%'
             AND TABLE_NAME != 'DATABASECHANGELOG'
             AND TABLE_TYPE != 'VIEW'
             ORDER BY TABLE_NAME ASC"
        );

        $tableStatement->execute(['schema' => $this->databaseName]);
        $tables = array_column($tableStatement->fetchAll(PDO::FETCH_ASSOC), 'TABLE_NAME');

        $result = [];
        if ($query->getIsPostCheck()) {
            $result['tableRowCountCheck'] = $this->postRunCheckTotals($tables);
            $result['undeletedRecordsCheck'] = $this->postRunUndeletedCheck($tables);
        } else {
            $result['tableCounts'] = $this->preRunTableCounts($tables);
            $result['expectedDeletes'] = $this->preRunExpectedRows();
        }

        return $result;
    }

    /**
     * Check row totals vs pre-run totals table.
     *
     * @param array $tables Array of table names
     * @return array|bool
     */
    private function postRunCheckTotals(array $tables)
    {
        foreach ($tables as $table) {
            $tableEsc = $this->escapeMysqlIdentifier($table);

            // Get row totals per table that were expected to be deleted
            $toDeleteStmt = $this->connection->prepare(
                "SELECT COUNT(primarykey) FROM DR_EXPECTED_DELETES WHERE tablename = :tablename"
            );
            $toDeleteStmt->execute([':tablename' => $table]);
            $toDeleteTotals[$table] = $toDeleteStmt->fetch(PDO::FETCH_COLUMN);

            // Count number of rows currently in each table
            $selectStmt = $this->connection->prepare(
                "SELECT COUNT(1) as rowcount FROM $tableEsc"
            );
            $selectStmt->execute(['schema' => $this->databaseName]);
            $currentTotals[$table] = $selectStmt->fetch(PDO::FETCH_COLUMN);

            // Get the row totals persisted before the delete
            $prevStmt = $this->connection->prepare(
                "SELECT rowcount as rowcount FROM DR_TABLE_COUNTS WHERE tablename = :tablename"
            );
            $prevStmt->execute([':tablename' => $table]);
            // subtract the number od expected deletions from previous totals to get what we now expect
            $expectedTotals[$table] = $prevStmt->fetch(PDO::FETCH_COLUMN) - $toDeleteTotals[$table];

            // Diff the current and expected arrays - array of mismatched tables will be returned for reporting.
            $difference = array_diff_assoc($expectedTotals, $currentTotals);

            $comparativeDiff = [];
            // Iterate through differences array to add comparative current counts.
            foreach ($difference as $table => $count) {
                // Running the DR Process always creates a new queue entry so bump this rowcount by 1
                $count = $table == 'queue' ? $count + 1 : $count;
                if ($count != $currentTotals[$table]) {
                    $comparativeDiff[$table]['expected'] = $count;
                    $comparativeDiff[$table]['found'] = $currentTotals[$table];
                }
            }
        }
        return empty($comparativeDiff)
            ? true
            : $comparativeDiff;
    }

    /**
     * Persist table of rowcounts
     *
     * @param array $tables Array of table names
     * @return array
     */
    private function preRunTableCounts(array $tables)
    {
        // Truncate the table storing counts
        $truncStatement = $this->connection->prepare('TRUNCATE TABLE DR_TABLE_COUNTS;');
        $truncStatement->execute(['schema' => $this->databaseName]);
        $truncStatement->closeCursor();

        // For every tale, get rowcount and insert into rowcounts table.
        foreach ($tables as $table) {
            $tableEsc = $this->escapeMysqlIdentifier($table);
            $insertStmt = $this->connection->prepare(
                "INSERT INTO DR_TABLE_COUNTS (tablename, `rowcount`) VALUES (
                             :tablename, (SELECT COUNT(1) FROM $tableEsc)
                         )"
            );
            $insertStmt->execute([':tablename' => $table]);
        }

        // Do a select against the newly populated table and return the values for the pre-run JSON report.
        $selectStmt = $this->connection->prepare(
            "SELECT * FROM DR_TABLE_COUNTS"
        );
        $selectStmt->execute(['schema' => $this->databaseName]);
        $tableCounts = $selectStmt->fetchAll(PDO::FETCH_ASSOC);
        $selectStmt->closeCursor();
        return $tableCounts;
    }

    /**
     * query for rows which should have been deleted - report if they still exist
     *
     * @param array $tables Array of table names
     * @return array|bool
     */
    private function postRunUndeletedCheck(array $tables)
    {
        $undeletedRows = [];
        // Get Primary keys for all tables.
        $pksByTable = $this->getPksByTable();
        foreach ($tables as $table) {
            $tableEsc = $this->escapeMysqlIdentifier($table);
            // Attempt to select rows which should have been deleted in a delete run.
            $selectExpDeletedRows = $this->connection->prepare(
                "SELECT {$pksByTable[$table][0]} FROM $tableEsc WHERE {$pksByTable[$table][0]} 
                          IN (SELECT primarykey FROM DR_EXPECTED_DELETES WHERE tablename = :tablename)"
            );
            $selectExpDeletedRows->execute([':tablename' => $this->databaseName]);
            $rows = $selectExpDeletedRows->fetchAll(PDO::FETCH_COLUMN);

            // ideally rows will always be empty, if not then add to array indexed by table-name for reporting.
            if (!empty($rows)) {
                $undeletedRows[$table] = $rows;
            }
        }

        return empty($undeletedRows)
            ? true
            : $undeletedRows;
    }

    /**
     * Populate table of rows that are expected to be deleted by Data Retention delete process.
     *
     * @return array
     */
    private function preRunExpectedRows()
    {
        $pksByTable = $this->getPksByTable();
        $fks = $this->getForeignKeys();

        $queries =
            [
                "DROP TEMPORARY TABLE IF EXISTS tmp_copy_data_retention",
                "SET @dr_delete_limit = (SELECT param_value FROM system_parameter WHERE id = 'DR_DELETE_LIMIT');
                PREPARE tempstatement FROM 'CREATE TEMPORARY TABLE tmp_copy_data_retention
                SELECT data_retention.*
                FROM data_retention
                         JOIN data_retention_rule ON data_retention.data_retention_rule_id = data_retention_rule.id
                WHERE data_retention.action_confirmation = 1
                  AND data_retention.deleted_date IS NULL
                  AND data_retention_rule.is_custom_rule = 0
                ORDER BY data_retention.id LIMIT ?';
                EXECUTE tempstatement USING @dr_delete_limit"
            ];

        $queries = array_merge($queries, $this->createTempTableQueries($pksByTable));
        $queries = array_merge($queries, $this->generateFkQueries($pksByTable, $fks));
        $queries = array_merge($queries, $this->generatePersistQueries($pksByTable));

        foreach ($queries as $query) {
            $statement = $this->connection->prepare($query);
            $statement->execute(['schema' => $this->databaseName]);
        }
        return $statement->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * @return array
     */
    private function getForeignKeys()
    {
        $constraintStatement = $this->connection->prepare(
            "
            SELECT
              `TABLE_SCHEMA`,
              `TABLE_NAME`,
              `COLUMN_NAME`,
              `CONSTRAINT_NAME`,
              `REFERENCED_TABLE_SCHEMA`,
              `REFERENCED_TABLE_NAME`,
              `REFERENCED_COLUMN_NAME`
            FROM
              information_schema.KEY_COLUMN_USAGE
            WHERE
              (`CONSTRAINT_SCHEMA` LIKE :schema
               OR TABLE_SCHEMA LIKE :schema
               OR REFERENCED_TABLE_SCHEMA LIKE :schema
              )
              AND `REFERENCED_TABLE_SCHEMA` IS NOT NULL
            "
        );

        try {
            $constraintStatement->execute(['schema' => $this->databaseName]);
            $fks = $constraintStatement->fetchAll(PDO::FETCH_ASSOC);
        } finally {
            $constraintStatement->closeCursor();
        }
        return $fks;
    }

    /**
     * Return array of primary keys for each table.
     *
     * @return array
     */
    private function getPksByTable()
    {
        $pksByTable = [];
        try {
            $primaryKeyStatement = $this->connection->prepare(
                "SELECT TABLE_NAME, COLUMN_NAME
                 FROM INFORMATION_SCHEMA.columns
                 WHERE TABLE_SCHEMA = :db AND COLUMN_KEY = 'PRI'"
            );
            $primaryKeyStatement->execute(['db' => $this->databaseName,]);

            foreach ($primaryKeyStatement->fetchAll(PDO::FETCH_ASSOC) as $pkInfo) {
                if (!array_key_exists($pkInfo['TABLE_NAME'], $pksByTable)) {
                    $pksByTable[$pkInfo['TABLE_NAME']] = [];
                }
                $pksByTable[$pkInfo['TABLE_NAME']][] = $pkInfo['COLUMN_NAME'];
            }
        } finally {
            $primaryKeyStatement->closeCursor();
        }
        return $pksByTable;
    }

    /**
     * Creates SQL for temp tables used in expected delete population
     *
     * @param array $pksByTable
     * @return array
     */
    private function createTempTableQueries(array $pksByTable)
    {
        $queries = [];
        foreach ($pksByTable as $table => $pks) {
            $tmpExpectedDeleteTableEsc = $this->escapeMysqlIdentifier("tmp_expected_delete_$table");
            $tmpGenerateDeleteTableEsc = $this->escapeMysqlIdentifier("tmp_generate_delete_$table");
            $tableEsc = $this->escapeMysqlIdentifier($table);
            $pksEsc = implode(", ", array_map([$this, 'escapeMysqlIdentifier'], $pks));
            $queries = array_merge(
                $queries,
                [
                    "DROP TEMPORARY TABLE IF EXISTS {$tmpGenerateDeleteTableEsc}",
                    "CREATE TEMPORARY TABLE {$tmpGenerateDeleteTableEsc} SELECT {$pksEsc} FROM {$tableEsc} LIMIT 0",
                    "ALTER TABLE {$tmpGenerateDeleteTableEsc} ADD PRIMARY KEY ($pksEsc)",
                    "DROP TEMPORARY TABLE IF EXISTS {$tmpExpectedDeleteTableEsc}",
                    "CREATE TEMPORARY TABLE {$tmpExpectedDeleteTableEsc} SELECT {$pksEsc} FROM {$tableEsc} LIMIT 0",
                    "ALTER TABLE {$tmpExpectedDeleteTableEsc} ADD PRIMARY KEY ($pksEsc)",
                ]
            );
        }

        foreach ($pksByTable as $table => $pks) {
            $tmpExpectedDeleteTableEsc = $this->escapeMysqlIdentifier("tmp_expected_delete_$table");
            $tableQuot = $this->connection->quote($table);
            if (count($pksByTable[$table]) === 1) {
                $queries[] = "INSERT IGNORE INTO {$tmpExpectedDeleteTableEsc}
                              SELECT entity_pk FROM tmp_copy_data_retention WHERE entity_name = {$tableQuot}";
            }
        }
        return $queries;
    }

    /**
     * Generate INSERTs for temp table for expected deletes.
     *
     * @param $pksByTable
     * @param $fks
     * @return array
     */
    private function generateFkQueries(array $pksByTable, array $fks)
    {
        $queries = [];
        foreach ($fks as $fk) {
            if ([$fk['REFERENCED_COLUMN_NAME']] !== $pksByTable[$fk['REFERENCED_TABLE_NAME']]) {
                throw new RuntimeException("FK/PK mismatch for " . $fk['CONSTRAINT_NAME']);
            }
            if ($fk['TABLE_SCHEMA'] !== $fk ['REFERENCED_TABLE_SCHEMA']) {
                throw new RuntimeException("Schema mismatch found for" . $fk['CONSTRAINT_NAME']);
            }
            $tmpExpectedDeleteTableEsc = $this->escapeMysqlIdentifier("tmp_expected_delete_{$fk['TABLE_NAME']}");
            $depTmpExpectedDeleteTableEsc = $this->escapeMysqlIdentifier(
                "tmp_expected_delete_{$fk['REFERENCED_TABLE_NAME']}"
            );
            $tmpGenerateDeleteTableEsc = $this->escapeMysqlIdentifier("tmp_generate_delete_{$fk['TABLE_NAME']}");
            $tableEsc = $this->escapeMysqlIdentifier($fk['TABLE_NAME']);
            $pksEsc = implode(", ", array_map([$this, 'escapeMysqlIdentifier'], $pksByTable[$fk['TABLE_NAME']]));
            $depPkEsc = $this->escapeMysqlIdentifier($pksByTable[$fk['REFERENCED_TABLE_NAME']][0]);
            $fkEsc = $this->escapeMysqlIdentifier($fk['COLUMN_NAME']);
            $queries = array_merge(
                $queries,
                [
                    "TRUNCATE TABLE {$tmpGenerateDeleteTableEsc}",
                    "INSERT INTO {$tmpGenerateDeleteTableEsc}
                     SELECT {$pksEsc}
                     FROM {$tableEsc}
                     WHERE {$tableEsc}.{$fkEsc} IN (
                       SELECT {$depTmpExpectedDeleteTableEsc}.{$depPkEsc}
                       FROM {$depTmpExpectedDeleteTableEsc}
                     )",
                    "INSERT IGNORE INTO {$tmpExpectedDeleteTableEsc} SELECT * FROM {$tmpGenerateDeleteTableEsc}",
                ]
            );
        }
        return $queries;
    }

    /**
     * Generate queries to persist data into DR_EXPECTED_DELETES table
     *
     * @param array $pksByTable
     * @return array
     */
    private function generatePersistQueries(array $pksByTable)
    {
        $queries[] = "TRUNCATE TABLE DR_EXPECTED_DELETES";
        foreach ($pksByTable as $table => $pks) {
            $tmpExpectedDeleteTableEsc = $this->escapeMysqlIdentifier("tmp_expected_delete_$table");
            $pksEsc = implode(", ", array_map([$this, 'escapeMysqlIdentifier'], $pks));
            $tableQuot = $this->connection->quote($table);
            $queries[] = "INSERT INTO DR_EXPECTED_DELETES (tablename, primarykey) 
                          SELECT $tableQuot, CONCAT_WS(',', {$pksEsc}) FROM {$tmpExpectedDeleteTableEsc}";
        }
        // Final statement selects values from table for JSON report
        $queries[] = "SELECT * FROM DR_EXPECTED_DELETES";
        return $queries;
    }
}

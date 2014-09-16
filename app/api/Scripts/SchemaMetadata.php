<?php

/**
 * Get schema metadata
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Cli;

use Pdo;

/**
 * Get schema metadata
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class SchemaMetadata
{
    const HELP = 'Usage \'php SchemaMetadata.php -uroot -ppassword -dolcs > /path/to/output.txt\'';

    const TABLE_FORMAT = "%s\n-------------------------\n";

    const COLUMN_FORMAT = "%s\n";

    /**
     * Store the cli options
     *
     * @var array
     */
    private $options;

    /**
     * Holds the PDO connection
     *
     * @var \Pdo
     */
    private $pdo;

    /**
     * Required params
     *
     * @var array
     */
    private $requiredParams = array(
        'u' => '',
        'p' => '',
        'd' => ''
    );

    /**
     * Output colours
     *
     * @var array
     */
    private $colors = array(
        'default' => "\e[0m",
        'info' => "\e[0;34m",
        'error' => "\e[0;31m",
        'success' => "\e[0;32m",
        'warning' => "\e[0;33m"
    );

    /**
     * Initialise the variables
     */
    public function __construct()
    {
        chdir(__DIR__ . '/../');

        require_once(__DIR__ . '/../init_autoloader.php');

        $this->options = getopt(
            'u:p:d:',
            array('help')
        );

        if (isset($this->options['help'])) {
            $this->exitResponse(self::HELP);
        }

        $this->checkForRequiredParams();
    }

    /**
     * Run the script
     */
    public function run()
    {
        $this->createDatabaseConnection();

        $this->outputMetadata();
    }

    /**
     * Create the database connection
     */
    private function createDatabaseConnection()
    {
        try {
            $this->pdo = new Pdo(
                'mysql:dbname=' . $this->options['d'] . ';host=localhost',
                $this->options['u'],
                $this->options['p'],
                array(Pdo::ATTR_ERRMODE => Pdo::ERRMODE_EXCEPTION)
            );
        } catch (\Exception $ex) {
            $this->exitResponse($ex->getMessage(), 'error');
        }
    }

    /**
     * Output metadata
     */
    private function outputMetadata()
    {
        $tableQueryString = 'SELECT table_name FROM information_schema.tables WHERE table_schema = \'%s\' '
            . 'ORDER BY table_name';

        $tableQuery = $this->pdo->prepare(
            sprintf(
                $tableQueryString,
                $this->options['d']
            )
        );

        $tableQuery->execute();

        $tables = $tableQuery->fetchAll(Pdo::FETCH_ASSOC);

        foreach ($tables as $row) {

            echo sprintf(self::TABLE_FORMAT, $row['table_name']);

            $columnQueryString = 'SELECT column_name, column_type FROM information_schema.columns'
                . ' WHERE table_schema = \'%s\' AND table_name = \'%s\' ORDER BY column_name';

            $columnQuery = $this->pdo->prepare(
                sprintf(
                    $columnQueryString,
                    $this->options['d'],
                    $row['table_name']
                )
            );

            $columnQuery->execute();

            $columns = $columnQuery->fetchAll(Pdo::FETCH_ASSOC);

            foreach ($columns as $columnRow) {
                echo sprintf(
                    self::COLUMN_FORMAT,
                    $columnRow['column_name']
                    //,$columnRow['column_type']
                );
            }

            echo "\n";
        }
    }

    /**
     * Respond
     *
     * @param string $message
     * @param string $type
     */
    private function respond($message, $type = 'default')
    {
        echo $this->colors[$type];

        if ($type != 'default') {
            echo ucwords($type) . ': ';
        }

        echo $message . $this->colors['default'] . "\n";
    }

    /**
     * Respond and exit
     *
     * @param string $message
     * @param string $type
     */
    private function exitResponse($message, $type = 'default')
    {
        $this->respond($message, $type);
        exit;
    }

    /**
     * Check for required options
     */
    private function checkForRequiredParams()
    {
        $missingParams = array();

        foreach ($this->requiredParams as $key => $val) {
            if (!isset($this->options[$key]) || empty($this->options[$key])) {
                $missingParams[] = $key;
            }
        }

        if (!empty($missingParams)) {
            $this->exitResponse(
                'You are missing the following required params: ' . implode(', ', $missingParams),
                'error'
            );
        }
    }
}

$cli = new SchemaMetadata();
$cli->run();

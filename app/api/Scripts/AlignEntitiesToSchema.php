<?php

/**
 * Align Entities To Schema
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Cli;

use DirectoryIterator;
use Pdo;

/**
 * Align Entities To Schema
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class AlignEntitiesToSchema
{
    const PATH_TO_DOCTRINE = '/workspace/OLCS/olcs-backend/vendor/bin/doctrine-module';

    const ENTITY_NAMESPACE = 'OlcsEntities\\Entity\\';

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
        'd' => '',
        'mapping-files' => '',
        'entity-files' => ''
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

        $this->options = getopt('u:p:d:', array('import-schema:', 'mapping-files:', 'entity-files:'));

        $this->checkForRequiredParams();
    }

    /**
     * Run the script
     */
    public function run()
    {
        // Steps
        // X Import Schema
        // X Remove old mapping files
        // X Generate mapping files
        // X Remove Old Entities
        // X Remove Old Traits
        // - Compile entity config
        // - Create traits
        // - Create entities
        $this->createDatabaseConnection();

        $this->maybeImportSchema();

        $this->removeOldMappingFiles();

        $this->generateNewMappingFiles();

        $this->removeOldEntities();

        $this->removeOldTraits();

        $this->compileEntityConfig();
    }

    /**
     * Create the database connection
     */
    private function createDatabaseConnection()
    {
        $this->respond('Connecting to databse...', 'info');

        try {
            $this->pdo = new Pdo(
                'mysql:dbname=' . $this->options['d'] . ';host=localhost',
                $this->options['u'],
                $this->options['p'],
                array(Pdo::ATTR_ERRMODE => Pdo::ERRMODE_EXCEPTION)
            );
            $this->respond('Connection successful', 'success');
        } catch (\Exception $ex) {
            $this->exitResponse($ex->getMessage(), 'error');
        }
    }

    /**
     * Import the schema if we want to import it
     */
    private function maybeImportSchema()
    {
        if (isset($this->options['import-schema'])) {

            $schema = $this->options['import-schema'];

            $this->respond('Importing schema: ' . $schema, 'info');

            $importSchemaCommand = 'mysql -u%s -p%s %s < %s';

            shell_exec(
                sprintf(
                    $importSchemaCommand,
                    $this->options['u'],
                    $this->options['p'],
                    $this->options['d'],
                    $schema
                )
            );
        }
    }

    /**
     * Remove old mapping files
     */
    private function removeOldMappingFiles()
    {
        $this->respond('Removing old mapping files...', 'info');

        $error = false;

        foreach (new DirectoryIterator($this->options['mapping-files']) as $fileInfo) {
            if ($fileInfo->isDot()) {
                continue;
            }

            $fileName = $fileInfo->getFilename();

            if (strstr($fileName, '.dcm.xml')) {
                unlink($this->options['mapping-files'] . $fileName);
                if (!file_exists($this->options['mapping-files'] . $fileName)) {
                    $this->respond('Removed: ' . $fileName);
                } else {
                    $error = true;
                    $this->respond('Unable to remove: ' . $fileName, 'error');
                }
            }
        }

        if ($error) {
            $this->exitResponse('Unable to remove some mapping files');
        } else {
            $this->respond('Old mapping files removed', 'success');
        }
    }

    /**
     * Generate new mapping files
     */
    private function generateNewMappingFiles()
    {
        $this->respond('Generating new mapping files...', 'info');

        $command = '%s orm:convert-mapping --namespace="%s" --force --from-database xml %s';

        shell_exec(
            sprintf(
                $command,
                self::PATH_TO_DOCTRINE,
                str_replace('\\', '\\\\', self::ENTITY_NAMESPACE),
                $this->options['mapping-files']
            )
        );

        $this->respond('Generated new files', 'success');
    }

    /**
     * Remove old entities
     */
    private function removeOldEntities()
    {
        $this->respond('Removing old entities...', 'info');

        $entityDirectory = $this->options['entity-files'] . str_replace('\\', '/', self::ENTITY_NAMESPACE);

        $error = false;

        foreach (new DirectoryIterator($entityDirectory) as $fileInfo) {

            if ($fileInfo->isDot() || $fileInfo->isDir()) {
                continue;
            }

            $fileName = $fileInfo->getFilename();

            unlink($entityDirectory . $fileName);
            if (!file_exists($entityDirectory . $fileName)) {
                $this->respond('Removed: ' . $fileName);
            } else {
                $error = true;
                $this->respond('Unable to remove: ' . $fileName, 'error');
            }
        }

        if ($error) {
            $this->exitResponse('Unable to remove some entities');
        } else {
            $this->respond('Old entities were removed', 'success');
        }
    }

    /**
     * Remove old traits
     */
    private function removeOldTraits()
    {
        $this->respond('Removing non-custom old traits ...', 'info');

        $entityDirectory = $this->options['entity-files'] . str_replace('\\', '/', self::ENTITY_NAMESPACE) . 'Traits/';

        $error = false;

        foreach (new DirectoryIterator($entityDirectory) as $fileInfo) {

            if ($fileInfo->isDot() || $fileInfo->isDir()) {
                continue;
            }

            $fileName = $fileInfo->getFilename();

            if (preg_match('/^Custom/', $fileName)) {
                continue;
            }

            unlink($entityDirectory . $fileName);
            if (!file_exists($entityDirectory . $fileName)) {
                $this->respond('Removed: ' . $fileName);
            } else {
                $error = true;
                $this->respond('Unable to remove: ' . $fileName, 'error');
            }
        }

        if ($error) {
            $this->exitResponse('Unable to remove some traits');
        } else {
            $this->respond('Old non-custom traits were removed', 'success');
        }
    }

    /**
     * Compile entity configs
     */
    private function compileEntityConfig()
    {

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

$cli = new AlignEntitiesToSchema();
$cli->run();

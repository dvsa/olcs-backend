<?php

/**
 * Align Entities To Schema
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Cli;

use Pdo;
use DirectoryIterator;
use Zend\Filter\Word\CamelCaseToSeparator;
use Zend\Mvc\Application;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * Align Entities To Schema
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class AlignEntitiesToSchema
{
    const ENTITY_NAMESPACE = 'Dvsa\\Olcs\\Api\\Entity\\';

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
        'entity-files' => '',
        'test-files' => '',
        'entity-config' => ''
    );

    private $defaultOptions = [
        'u' => 'root',
        'p' => 'password',
        'd' => 'olcs_be',
        'mapping-files' => '/var/www/olcs/olcs-backend/data/mapping/',
        'entity-files' => '/var/www/olcs/olcs-backend/module/Api/src/Entity/',
        'test-files' => '/var/www/olcs/olcs-backend/test/module/Api/src/Entity/',
        'entity-config' => '/var/www/olcs/olcs-backend/data/db/EntityConfig.php'
    ];

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
     * Option format mapping
     *
     * @var array
     */
    private $optionFormat = array(
        'name' => array(
            'column' => '',
            'join-column' => 'name="%s"',
            'join-table' => 'name="%s"',
            'indexes' => 'name="%s"',
            'unique-constraints' => 'name="%s"'
        ),
        'fetch' => 'fetch="%s"',
        'columns' => 'columns={"%s"}',
        'field' => '',
        'type' => 'type="%s"',
        'column' => 'name="%s"',
        'length' => 'length=%s',
        'nullable' => 'nullable=%s',
        'target-entity' => 'targetEntity="%s"',
        'referenced-column-name' => 'referencedColumnName="%s"',
        'mapped-by' => array(
            'column' => 'mappedBy="%s"',
            'manyToOne' => 'mappedBy="%ss"',
            'manyToMany' => 'mappedBy="%ss"',
            'oneToOne' => 'mappedBy="%s"'
        ),
        'cascade' => 'cascade={"%s"}',
        'inversed-by' => array(
            'column' => 'inversedBy="%s"',
            'manyToOne' => 'inversedBy="%ss"',
            'manyToMany' => 'inversedBy="%ss"',
            'oneToOne' => 'inversedBy="%s"'
        ),
        'indexBy' => 'indexBy="%s"',
        'orphanRemoval' => 'orphanRemoval=%s',
        'order-by' => '{"%s"}'
    );

    /**
     * Holds the mapping files
     *
     * @var array
     */
    private $mappingFiles = array();

    /**
     * Holds the entity config
     *
     * @var array
     */
    private $entities = array();

    /**
     * Fields
     *
     * @var array
     */
    private $fields = array();

    /**
     * Field Details
     *
     * @var array
     */
    private $fieldDetails = array();

    /**
     * Holds any additional inverse fields;
     *
     * @var array
     */
    private $inverseFields = array();

    /**
     * Cache table descriptions
     *
     * @var array
     */
    private $tableDescription = array();

    /**
     * Holds the entity config
     *
     * @var array
     */
    private $entityConfig = array();

    private $application;

    /**
     * Initialise the variables
     */
    public function __construct()
    {
        $this->options = getopt(
            'u:p:d:',
            array('help', 'default', 'import-schema:', 'mapping-files:', 'entity-files:', 'test-files:', 'entity-config:')
        );

        if (isset($this->options['help'])) {
            $this->exitResponse(
                "\n\n=========================================\n"
                . "Default options: \n"
                . "=========================================\n\n"
                . "     import-schema    /var/www/olcs/olcs-etl/olcs_schema.sql \n"
                . "     mapping-files    " . $this->defaultOptions['mapping-files'] . " \n"
                . "     entity-files     " . $this->defaultOptions['entity-files'] . " \n"
                . "     test-files       " . $this->defaultOptions['test-files'] . " \n"
                . "     entity-config    " . $this->defaultOptions['entity-config'] . " \n"
                . "     -u               " . $this->defaultOptions['u'] . " \n"
                . "     -p               " . $this->defaultOptions['p'] . " \n"
                . "     -d               " . $this->defaultOptions['d'] . " \n"
                . " \n\n"
                . "=========================================\n"
                . "Sample usage with default options \n"
                . "=========================================\n\n"
                . "'php AlignEntitiesToSchema.php --default'\n"
                . " \n\n"
                . "=========================================\n"
                . "Sample Usage with custom options \n"
                . "=========================================\n\n"
                . "'php AlignEntitiesToSchema.php --import-schema /var/www/olcs/olcs-etl/olcs_schema.sql "
                . "--mapping-files " . $this->defaultOptions['mapping-files'] . " "
                . "--entity-files " . $this->defaultOptions['entity-files'] . " "
                . "--test-files " . $this->defaultOptions['test-files'] . " "
                . "--entity-config " . $this->defaultOptions['entity-config'] . " "
                . "-u" . $this->defaultOptions['u'] . " "
                . "-p" . $this->defaultOptions['p'] . " "
                . "-d" . $this->defaultOptions['d'] . "'\n\n"
            );
        }

        if (isset($this->options['default'])) {
            foreach ($this->defaultOptions as $option => $value) {
                $this->options[$option] = $value;
            }
        }

        $this->checkForRequiredParams();
    }

    /**
     * Run the script
     */
    public function run($config)
    {

        $this->respond('Building from existing database', 'info');

        $this->application = Application::init($config);

        try {
            $this->loadEntityConfig();

            $this->createDatabaseConnection();

            $this->removeHistTables();
            $this->removeLiquibaseTables();

//            $this->maybeImportSchema();

            $this->maybeCreateDir($this->options['mapping-files']);

            $this->removeOldMappingFiles();

            $this->generateNewMappingFiles();

            $this->findMappingFiles();

            $this->removeOldEntities();

            $this->compileEntityConfig();

            $this->createEntities();

            //$this->removeOldUnitTests();

            $this->createUnitTests();

            $this->importEntities();

            $this->rebuildDbUsingLiquidbase();

            $this->restartApache();
        } catch (\Exception $ex) {
            echo $ex->getTraceAsString() . "\n\n";
            echo $ex->getMessage();
            exit;
        }
    }

    /**
     * Load the entity config
     */
    private function loadEntityConfig()
    {
        $this->entityConfig = include($this->options['entity-config']);

        ksort($this->entityConfig, SORT_NATURAL);
    }

    /**
     * Create the database connection
     */
    private function createDatabaseConnection()
    {
        $this->respond('Connecting to database...', 'info');

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

    private function removeHistTables()
    {
        $this->respond('Removing _hist tables', 'info');

        $mysqlCommand = sprintf(
            'mysql -u%s -p%s %s',
            $this->options['u'],
            $this->options['p'],
            $this->options['d']
        );

        // SQL to generate DROP statements for all _hist tables
        $sql = 'SELECT CONCAT(\'DROP TABLE \', t.TABLE_NAME, \';\') AS \'-- DROP _hist tables\'FROM information_schema.TABLES t 
          WHERE t.TABLE_SCHEMA = \''. $this->options['d'] .'\'
          AND t.TABLE_NAME LIKE \'%_hist\'';

        // run the SQL to get the DROP statements
        $dropHistTablesSql = shell_exec($mysqlCommand .' -e "'. $sql .'"');

        // execute the DROP statments
        $output = shell_exec($mysqlCommand .' -e "'. $dropHistTablesSql .'"');
    }

    private function removeLiquibaseTables()
    {
        $this->respond('Removing Liquibase tables', 'info');

        $mysqlCommand = sprintf(
            'mysql -u%s -p%s %s',
            $this->options['u'],
            $this->options['p'],
            $this->options['d']
        );

        shell_exec($mysqlCommand .' -e "DROP TABLE IF EXISTS DATABASECHANGELOG"');
        shell_exec($mysqlCommand .' -e "DROP TABLE IF EXISTS DATABASECHANGELOGLOCK"');
        shell_exec($mysqlCommand .' -e "DROP TABLE IF EXISTS log_update"');
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

            $this->recreateDatabase();

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
     * Rebuild the database
     */
    private function recreateDatabase()
    {
        $dropDatabase = 'mysql -u%s -p%s -e \'DROP DATABASE IF EXISTS %s\'';

        $createDatabase = 'mysql -u%s -p%s -e \'CREATE DATABASE IF NOT EXISTS %s\'';

        shell_exec(
            sprintf(
                $dropDatabase,
                $this->options['u'],
                $this->options['p'],
                $this->options['d']
            )
        );

        shell_exec(
            sprintf(
                $createDatabase,
                $this->options['u'],
                $this->options['p'],
                $this->options['d']
            )
        );
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
        $this->respond('Generating new mapping files from: ' . $this->entityConfig['mappingConfig'], 'info');

        // Setup the custom mapping object
        $command = new ConvertMappingCommand($this->entityConfig['mappingConfig']);

        /**
         * Grab the doctrine cli runner
         *
         * @var \Symfony\Component\Console\Application $cli
         */
        $cli = $this->application->getServiceManager()->get('doctrine.cli');
        // Prevent auto exit
        $cli->setAutoExit(false);
        // Add the custom command
        $cli->add($command);

        // Mock the cli input
        $argv = new ArgvInput(
            [
                'blah',
                'orm:convert-mapping',
                '--namespace=' . self::ENTITY_NAMESPACE,
                '--force',
                '--from-database',
                'xml',
                $this->options['mapping-files']
            ]
        );

        // Create the output object
        $output = new BufferedOutput();

        // Run the command
        $cli->run($argv, $output);
        $content = $output->fetch();

        $this->respond('Generated new files', 'success');
    }

    /**
     * Remove old entities
     */
    private function removeOldEntities()
    {
        $this->respond('Removing old entities...', 'info');

        $entityDirectory = $this->options['entity-files'];

        $error = false;

        $di = new \RecursiveDirectoryIterator($entityDirectory, \FilesystemIterator::SKIP_DOTS);
        $ri = new \RecursiveIteratorIterator($di, \RecursiveIteratorIterator::CHILD_FIRST);
        foreach ($ri as $file) {
            if ($file->isFile()) {
                $fileName = $file->getFilename();
                $filePath = $file->getPath() . '/' . $fileName;

                if (preg_match('/Abstract([^.]+).php/', $fileName)) {
                    unlink($filePath);
                    if (!file_exists($filePath)) {
                        $this->respond('Removed: ' . $filePath);
                    } else {
                        $error = true;
                        $this->respond('Unable to remove: ' . $filePath, 'error');
                    }
                }
            }
        }

        if ($error) {
            $this->exitResponse('Unable to remove some entities');
        } else {
            $this->respond('Old entities were removed', 'success');
        }
    }

    /**
     * Compile entity configs
     */
    private function compileEntityConfig()
    {
        $this->respond('Compiling entity configuration...', 'info');
        foreach ($this->mappingFiles as $className => $fileName) {
            $config = $this->getConfigFromMappingFile($fileName);

            $comments = $this->getCommentsFromTable($config['entity']['@attributes']['table']);

            if (isset($comments['@settings']['ignore'])) {
                continue;
            }

            $defaults = $this->getDefaultsFromTable($config['entity']['@attributes']['table']);

            $nullables = $this->getNullablesFromTable($config['entity']['@attributes']['table']);

            $camelCaseName = str_replace(self::ENTITY_NAMESPACE, '', $config['entity']['@attributes']['name']);

            $namespace = $this->findNamespace($camelCaseName);
            $relativeNamespace = $this->findRelativeNamespace($camelCaseName);

            $fields = $this->getFieldsFromConfig($config, $defaults, $comments, $camelCaseName, $nullables);

            $this->cacheFields($fields);

            $this->entities[$camelCaseName] = array(
                'name' => $camelCaseName,
                'softDeletable' => $this->hasSoftDeleteField($fields),
                'blameable' => $this->hasBlameableFields($fields),
                'translatable' => $this->hasTranslatableField($fields),
                'className' => $namespace . '\\' . $camelCaseName,
                'table' => $config['entity']['@attributes']['table'],
                'ids' => $this->getIdsFromFields($fields),
                'indexes' => $this->getIndexesFromConfig($config),
                'unique-constraints' => $this->getUniqueConstraintsFromConfig($config),
                'fields' => $fields,
                'hasCollections' => $this->getHasCollectionsFromConfig($config),
                'collections' => $this->getCollectionsFromConfig($config),
                'mappingFileName' => $fileName,
                'entityFileName' => $this->formatEntityFileName($className, $relativeNamespace),
                'entityConcreteFileName' => $this->formatEntityConcreteFileName($className, $relativeNamespace),
                'testFileName' => $this->formatUnitTestFileName($className, $relativeNamespace),
                'namespace' => $namespace,
                'hasCreatedOn' => $this->hasCreatedOn($fields),
                'hasModifiedOn' => $this->hasModifiedOn($fields)
            );

            if (isset($comments['@settings']['repository'])) {
                $this->entities[$camelCaseName]['repository'] = $comments['@settings']['repository'];
            }
        }

        ksort($this->entities, SORT_NATURAL);

        foreach ($this->entities as $className => &$details) {
            if (!isset($this->inverseFields[$className])) {
                continue;
            }

            foreach ($this->inverseFields[$className] as $fieldDetails) {
                $property = $fieldDetails['relationship'] == 'oneToMany' || $fieldDetails['relationship'] == 'oneToOne'
                    ? 'mapped-by' : 'inversed-by';

                $item = array(
                    '@attributes' => array(
                        'field' => $fieldDetails['property'],
                        'target-entity' => $this->replaceNamespace($fieldDetails['targetEntity']),
                        $property => $fieldDetails['inversedBy']
                    ),
                    'orderBy' => $fieldDetails['orderBy']
                );

                if (isset($fieldDetails['cascade'])) {
                    $item['@attributes']['cascade'] = $fieldDetails['cascade'];
                }

                if (isset($fieldDetails['indexBy'])) {
                    $item['@attributes']['indexBy'] = $fieldDetails['indexBy'];
                }

                if (isset($fieldDetails['orphanRemoval'])) {
                    $item['@attributes']['orphanRemoval'] = $fieldDetails['orphanRemoval'];
                }

                if (isset($fieldDetails['fetch'])) {
                    $item['@attributes']['fetch'] = $fieldDetails['fetch'];
                }

                $details['fields'][] = array(
                    'isId' => false,
                    'isInverse' => true,
                    'type' => $fieldDetails['relationship'],
                    'ref' => 'field',
                    'default' => null,
                    'config' => $item,
                    'isVersion' => false
                );

                if ($fieldDetails['relationship'] == 'manyToMany' || $fieldDetails['relationship'] == 'oneToMany') {
                    $details['hasCollections'] = true;

                    $details['collections'][] = $item;
                }
            }
        }

        $this->respond('Entity configurations compiled', 'success');
    }

    protected function hasCreatedOn($fields)
    {
        return isset($fields['createdOn']);
    }

    protected function hasModifiedOn($fields)
    {
        return isset($fields['lastModifiedOn']);
    }

    protected function replaceNamespace($namespace)
    {
        $parts = explode('\\', $namespace);

        $last = array_pop($parts);

        return $this->findNamespace($last) . '\\' . $last;
    }

    protected function findNamespace($name)
    {
        return rtrim(self::ENTITY_NAMESPACE . $this->findRelativeNamespace($name), '\\');
    }

    protected function findRelativeNamespace($name)
    {
        if (!isset($this->entityConfig['namespaces'][$name])) {
            return '';
        }

        return $this->entityConfig['namespaces'][$name];
    }

    /**
     * Get a list of id properties from the fields
     *
     * @param array $fields
     * @return array
     */
    private function getIdsFromFields($fields)
    {
        $ids = array();

        foreach ($fields as $field) {
            if ($field['isId']) {
                $ids[] = $this->formatPropertyName($field);
            }
        }

        return $ids;
    }

    /**
     * Format a property name
     *
     * @param array $item
     * @return string
     */
    private function formatPropertyName($item)
    {
        $field = $item['config'];

        $propertyName = '';

        if (isset($item['property'])) {
            $propertyName = $item['property'];
        } else {
            $propertyName = $field['@attributes'][$item['ref']];
        }

        if (in_array($item['type'], array('oneToMany', 'manyToMany'))) {
            $propertyName .= 's';
        }

        return $propertyName;
    }

    /**
     * Format param name
     *
     * @param array $item
     * @return string
     */
    private function formatParamName($item)
    {
        $property = $this->formatPropertyName($item);

        if (strlen($property) >= 40) {
            $property = 'input';
        }

        return $property;
    }

    /**
     * Check if there is a soft delete field
     *
     * @param array $fields
     */
    private function hasSoftDeleteField($fields)
    {
        foreach ($fields as $field) {
            if ($field['config']['@attributes'][$field['ref']] == 'deletedDate') {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if there is a translatable field
     *
     * @param array $fields
     */
    private function hasTranslatableField($fields)
    {
        foreach ($fields as $field) {
            if ($field['translatable']) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if there is a blameable fields
     *
     * @param array $fields
     * @return bool
     */
    private function hasBlameableFields($fields)
    {
        foreach ($fields as $field) {
            if ($this->isCreatedByField($field) || $this->isLastModifiedByField($field)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if it is a createdBy field
     *
     * @param array $field
     * @return bool
     */
    private function isCreatedByField($field)
    {
        return ($this->formatPropertyName($field) === 'createdBy');
    }

    /**
     * Check if it is a lastModifiedBy field
     *
     * @param array $field
     * @return bool
     */
    private function isLastModifiedByField($field)
    {
        return ($this->formatPropertyName($field) === 'lastModifiedBy');
    }

    /**
     * Check if the field is a createdOn field
     *
     * @param array $field array of field info
     *
     * @return bool
     */
    private function isCreatedOnField($field): bool
    {
        return ($this->formatPropertyName($field) === 'createdOn');
    }

    /**
     * Check if the field is a lastModifiedOn field
     *
     * @param array $field array of field info
     *
     * @return bool
     */
    private function isLastModifiedOnField($field): bool
    {
        return ($this->formatPropertyName($field) === 'lastModifiedOn');
    }

    /**
     * Check if the field is a deletedDate field
     *
     * @param array $field array of field info
     *
     * @return bool
     */
    private function isDeletedDateField(array $field): bool
    {
        return ($this->formatPropertyName($field) === 'deletedDate');
    }

    /**
     * Whether the field is stored in a trait rather than directly in the entity
     *
     * @param array $field array of field info
     *
     * @return bool
     */
    private function isPropertyFromTrait(array $field): bool
    {
        return $this->isCreatedOnField($field)
            || $this->isLastModifiedOnField($field)
            || $this->isDeletedDateField($field);
    }

    /**
     * Create entities
     */
    private function createEntities()
    {
        $this->respond('Creating entities...', 'info');

        $error = false;

        foreach ($this->entities as $className => $details) {
            ob_start();
                include(__DIR__ . '/templates/entity.phtml');
                $content = ob_get_contents();
            ob_end_clean();

            $this->maybeCreateDir($details['entityFileName']);

            file_put_contents($details['entityFileName'], $content);

            if (!file_exists($details['entityConcreteFileName'])) {
                ob_start();
                    include(__DIR__ . '/templates/concrete.phtml');
                    $content = ob_get_contents();
                ob_end_clean();

                file_put_contents($details['entityConcreteFileName'], $content);
            }

            if (file_exists($details['entityFileName'])) {
                $this->respond('Entity created: ' . $className);
            } else {
                $error = true;
                $this->respond('Entity creation failed: ' . $className, 'error');
            }
        }

        if ($error) {
            $this->exitResponse('Some entities were not created', 'error');
        } else {
            $this->respond('Entity created', 'success');
        }
    }

    protected function maybeCreateDir($fileName)
    {
        $parts = explode('/', $fileName);
        array_pop($parts);
        $dir = '/' . implode('/', $parts);

        if (!file_exists($dir)) {
            mkdir($dir);
        }
    }

    /**
     * Import entities
     */
    private function importEntities()
    {
        $this->respond('Importing entities...', 'info');

        $this->recreateDatabase();

        $output = shell_exec('vendor/bin/doctrine-module orm:schema:update --force');

        $this->respond($output, 'info');

        $this->respond('Entities imported', 'success');
    }

    private function rebuildDbUsingLiquidbase()
    {
        $this->respond('Rebuilding db using Liquidbase (olcs-etl)...', 'info');

        passthru('cd /var/www/olcs/olcs-etl/ && make create-db && make update', $result);

        if ($result !== 0) {
            $this->exitResponse('Unable to rebuild database', 'error');
        }

        $this->respond('Database rebuilt and updated', 'success');
    }

    private function restartApache()
    {
        $this->respond('Restarting Apache to clear APC...', 'info');

        passthru('sudo service httpd restart', $result);

        if ($result !== 0) {
            $this->exitResponse('Unable to restart Apache', 'error');
        }

        $this->respond('Apache restarted', 'success');
    }

    /**
     * Remove old unit tests
     */
    private function removeOldUnitTests()
    {
        $this->respond('Removing old entity unit tests...', 'info');

        $directory = $this->options['test-files'];
        $error = false;

        $di = new \RecursiveDirectoryIterator($directory, \FilesystemIterator::SKIP_DOTS);
        $ri = new \RecursiveIteratorIterator($di, \RecursiveIteratorIterator::CHILD_FIRST);
        foreach ($ri as $file) {
            if ($file->isFile()) {
                $fileName = $file->getFilename();
                $filePath = $file->getPath() . '/' . $fileName;

                if (preg_match('/([^.]+)EntityTest.php/', $fileName)) {
                    unlink($filePath);
                    if (!file_exists($filePath)) {
                        $this->respond('Removed: ' . $filePath);
                    } else {
                        $error = true;
                        $this->respond('Unable to remove: ' . $filePath, 'error');
                    }
                }
            }
        }

        if ($error) {
            $this->exitResponse('Unable to remove some entity unit tests');
        } else {
            $this->respond('Old entity tests were removed', 'success');
        }
    }

    /**
     * Create new unit tests
     */
    private function createUnitTests()
    {
        $this->respond('Creating new unit tests...', 'info');

        $error = false;

        foreach ($this->entities as $className => $details) {
            // Skip the file if it exists
            if (file_exists($details['testFileName'])) {
                continue;
            }

            ob_start();
                include(__DIR__ . '/templates/test-entity.phtml');
                $content = ob_get_contents();
            ob_end_clean();

            $this->maybeCreateDir($details['testFileName']);
            file_put_contents($details['testFileName'], $content);

            if (file_exists($details['testFileName'])) {
                $this->respond('Entity tests created: ' . $className);
            } else {
                $error = true;
                $this->respond('Entity tests creation failed: ' . $className, 'error');
            }
        }

        if ($error) {
            $this->exitResponse('Some entity unit tests were not created', 'error');
        } else {
            $this->respond('Entity unit tests created', 'success');
        }
    }

    /**
     * Cache Fields
     *
     * @param array $fields
     */
    private function cacheFields($fields)
    {
        foreach ($fields as $field) {
            $encode = json_encode($field);

            $key = array_search($encode, $this->fields);

            if ($key === false) {
                $this->fields[] = $encode;
                $this->fieldDetails[] = array(
                    'count' => 0
                );

                $key = count($this->fields) - 1;
            }

            $this->fieldDetails[$key]['count']++;
        }
    }

    /**
     * Check if config has collections
     *
     * @param array $config
     * @return int
     */
    private function getHasCollectionsFromConfig($config)
    {
        return (isset($config['entity']['many-to-many']) || isset($config['entity']['one-to-many']));
    }

    /**
     * Get collections from config
     *
     * @param array $config
     * @return array
     */
    private function getCollectionsFromConfig($config)
    {
        $collections = array();

        if (isset($config['entity']['many-to-many'])) {
            $collections = $this->standardiseArray($config['entity']['many-to-many']);
        }

        if (isset($config['entity']['one-to-many'])) {
            $collections = array_merge($collections, $this->standardiseArray($config['entity']['one-to-many']));
        }

        return $collections;
    }

    /**
     * Get defaults form table
     *
     * @param string $table
     * @return array
     */
    private function getDefaultsFromTable($table)
    {
        $description = $this->describeTable($table);

        $defaults = array();

        foreach ($description as $row) {
            $defaults[$row['Field']] = $row['Default'];
        }

        return $defaults;
    }

    /**
     * Get nullables form table
     *
     * @param string $table
     * @return array
     */
    private function getNullablesFromTable($table)
    {
        $description = $this->describeTable($table);

        $nullables = array();

        foreach ($description as $row) {
            $nullables[$row['Field']] = $row['Null'];
        }

        return $nullables;
    }

    /**
     * Get indexes from config
     *
     * @param array $config
     * @return array
     */
    private function getIndexesFromConfig($config)
    {
        return $this->standardiseArray(
            isset($config['entity']['indexes']['index']) ? $config['entity']['indexes']['index'] : array()
        );
    }

    /**
     * Get unique constraints from config
     *
     * @param array $config
     * @return array
     */
    private function getUniqueConstraintsFromConfig($config)
    {
        return $this->standardiseArray(
            isset($config['entity']['unique-constraints']['unique-constraint'])
            ? $config['entity']['unique-constraints']['unique-constraint']
            : array()
        );
    }

    /**
     * Get fields form config
     *
     * @param array $config
     * @param array $defaults
     * @param array $comments
     * @param array $nullables
     * @return array
     */
    private function getFieldsFromConfig($config, $defaults, $comments, $className, $nullables)
    {
        $fields = array();

        $ids = array();

        // Standardise the ids
        if (isset($config['entity']['id'])) {
            if (!is_numeric(array_keys($config['entity']['id'])[0])) {
                $ids[$config['entity']['id']['@attributes']['name']] = $config['entity']['id'];
            } else {
                foreach ($config['entity']['id'] as $id) {
                    $ids[$id['@attributes']['name']] = $id;
                }
            }
        }

        // Distinguish between association keys and fields
        $associationKeys = array();
        foreach ($ids as $item) {
            // Association keys are PKs that are FKs also
            if (isset($item['@attributes']['association-key'])) {
                $associationKeys[$item['@attributes']['name']] = true;
            } else {
                $columnName = $this->formatColumnFromItem($item, 'id');

                $default = isset($defaults[$columnName]) ? $defaults[$columnName] : null;

                $extraConfig = $this->getConfigFromComments($comments, $columnName);

                $fieldConfig = array(
                    'isId' => true,
                    'isInverse' => false,
                    'type' => 'id',
                    'ref' => 'name',
                    'default' => $default,
                    'config' => $item,
                    'translatable' => false,
                    'isVersion' => ($columnName === 'version')
                );

                if (isset($extraConfig['type'])) {
                    $fieldConfig['config']['@attributes']['type'] = $extraConfig['type'];
                    unset($extraConfig['type']);
                }

                $name = $this->formatPropertyName($fieldConfig);

                $fields[$name] = array_merge($fieldConfig, $extraConfig);
            }
        }

        // This checks if any many-to-one should actually be a one-to-one
        if (isset($config['entity']['unique-constraints']['unique-constraint'])) {
            foreach ($config['entity']['unique-constraints']['unique-constraint'] as $key => $uniqueConstraint) {
                if (is_numeric($key)) {
                    $attributes = $uniqueConstraint['@attributes'];
                } else {
                    $attributes = $uniqueConstraint;
                }

                if (!strstr($attributes['columns'], ',')
                    && isset($config['entity']['many-to-one'])
                ) {
                    $ukName = $attributes['columns'];

                    foreach ($config['entity']['many-to-one'] as $key => $manyToOneItem) {
                        if (isset($manyToOneItem['join-columns']['join-column']['@attributes']['name'])
                            && $manyToOneItem['join-columns']['join-column']['@attributes']['name'] == $ukName) {
                            $config['entity']['one-to-one'][] = $manyToOneItem;
                            unset($config['entity']['many-to-one'][$key]);
                        }
                    }
                }
            }
        }

        $manyToOne = $this->standardiseArray(
            isset($config['entity']['many-to-one']) ? $config['entity']['many-to-one'] : array()
        );
        $manyToMany = $this->standardiseArray(
            isset($config['entity']['many-to-many']) ? $config['entity']['many-to-many'] : array()
        );
        $oneToMany = $this->standardiseArray(
            isset($config['entity']['one-to-many']) ? $config['entity']['one-to-many'] : array()
        );
        $oneToOne = $this->standardiseArray(
            isset($config['entity']['one-to-one']) ? $config['entity']['one-to-one'] : array()
        );
        $field = $this->standardiseArray(
            isset($config['entity']['field']) ? $config['entity']['field'] : array()
        );

        foreach (['manyToOne', 'manyToMany', 'oneToMany', 'oneToOne', 'field'] as $which) {
            foreach ($$which as $item) {
                $key = ($which == 'field' ? 'name' : 'field');

                $columnName = $this->formatColumnFromItem($item, $which);

                $default = isset($defaults[$columnName]) ? $defaults[$columnName] : null;

                $extraConfig = $this->getConfigFromComments($comments, $columnName);

                if ($default !== null) {
                    $item['@attributes']['options'] = array('default' => $default);
                }

                if (isset($item['@attributes']['target-entity'])) {
                    $item['@attributes']['target-entity'] = $this->replaceNamespace(
                        $item['@attributes']['target-entity']
                    );
                }

                $fieldConfig = array(
                    'isId' => isset($associationKeys[$item['@attributes'][$key]]),
                    'isInverse' => false,
                    'type' => $which,
                    'ref' => ($which == 'field' ? 'name' : 'field'),
                    'default' => $default,
                    'config' => $item,
                    'translatable' => false,
                    'isVersion' => ($columnName === 'version')
                );

                if (isset($fieldConfig['config']['join-columns']['join-column'])) {
                    $fieldConfig['config']['join-columns']['join-column']['@attributes']['nullable'] =
                        $this->isNullable($columnName, $nullables);
                }

                if (isset($extraConfig['cascade'])) {
                    $cascade = $extraConfig['cascade'];

                    if (isset($fieldConfig['config']['@attributes']['cascade'])) {
                        $cascade = array_merge(
                            $extraConfig,
                            (array)$fieldConfig['config']['@attributes']['cascade']
                        );
                    }

                    $fieldConfig['config']['@attributes']['cascade'] = $cascade;
                }

                if (isset($extraConfig['type'])) {
                    $fieldConfig['config']['@attributes']['type'] = $extraConfig['type'];
                    unset($extraConfig['type']);
                }

                if (isset($extraConfig['fetch'])) {
                    $fieldConfig['config']['@attributes']['fetch'] = $extraConfig['fetch'];
                    unset($extraConfig['fetch']);
                }

                $fieldConfig = array_merge($fieldConfig, $extraConfig);

                if (isset($fieldConfig['inversedBy'])) {
                    if (isset($fieldConfig['config']['@attributes']['inversed-by'])) {
                        unset($fieldConfig['config']['@attributes']['inversed-by']);
                    }

                    if (!isset($this->inverseFields[$fieldConfig['inversedBy']['entity']])) {
                        $this->inverseFields[$fieldConfig['inversedBy']['entity']] = array();
                    }

                    switch ($fieldConfig['type']) {
                        case 'manyToOne':
                            $relationship = 'oneToMany';
                            break;
                        case 'oneToMany':
                            $relationship = 'manyToOne';
                            break;
                        default:
                            $relationship = $fieldConfig['type'];
                            break;
                    }

                    $this->inverseFields[$fieldConfig['inversedBy']['entity']][] = array(
                        'inversedBy' => $this->formatPropertyName($fieldConfig),
                        'targetEntity' => self::ENTITY_NAMESPACE . ucfirst($className),
                        'property' => $fieldConfig['inversedBy']['property'],
                        'relationship' => $relationship,
                        'cascade' => isset($fieldConfig['inversedBy']['cascade'])
                            ? $fieldConfig['inversedBy']['cascade']
                            : null,
                        'indexBy' => isset($fieldConfig['inversedBy']['indexBy'])
                            ? $fieldConfig['inversedBy']['indexBy']
                            : null,
                        'orphanRemoval' => isset($fieldConfig['inversedBy']['orphanRemoval'])
                            ? $fieldConfig['inversedBy']['orphanRemoval']
                            : null,
                        'orderBy' => isset($fieldConfig['inversedBy']['orderBy'])
                            ? $fieldConfig['inversedBy']['orderBy']
                            : null,
                        'fetch' => isset($fieldConfig['inversedBy']['fetch'])
                            ? $fieldConfig['inversedBy']['fetch']
                            : null
                    );

                    $fieldConfig['config']['@attributes']['inversed-by'] = $fieldConfig['inversedBy']['property'];
                }

                $name = $this->formatPropertyName($fieldConfig);

                $fields[$name] = $fieldConfig;
            }
        }

        ksort($fields, SORT_NATURAL);
        ksort($this->inverseFields, SORT_NATURAL);

        return $fields;
    }

    /**
     * Check if field is nullable
     *
     * @param string $field
     * @param array $nullables
     */
    private function isNullable($field, $nullables)
    {
        return $nullables[$field] == 'NO' ? 'false' : 'true';
    }

    /**
     * Get config from comments
     *
     * @param array $comments
     * @param string $key
     * @return array
     */
    private function getConfigFromComments($comments, $key)
    {
        if (!isset($comments[$key])) {
            return array();
        }

        return $comments[$key];
    }

    /**
     * Format column name from name
     *
     * @param string $name
     * @return string
     */
    private function formatColumnFromItem($item, $which)
    {
        if (isset($item['join-columns']['join-column']['@attributes']['name'])) {
            $name = $item['join-columns']['join-column']['@attributes']['name'];
        } else {
            $key = ($which == 'field' || $which == 'id' ? 'name' : 'field');
            $name = $item['@attributes'][$key];
            $filter = new CamelCaseToSeparator('_');
            $name = strtolower($filter->filter($name));

            $name = $this->processColumnFormatOverrides($name);
        }

        return $name;
    }

    /**
     * Handle special cases where the formatColumnFromItem isn't enough
     *
     * @param string $name
     * @return string
     */
    protected function processColumnFormatOverrides($name)
    {
        $overrides = [
            'section26' => 'section_26'
        ];

        return str_replace(array_keys($overrides), array_values($overrides), $name);
    }

    /**
     * Standardise the XML node arrays
     *
     * @param array $array
     * @return array
     */
    private function standardiseArray($array)
    {
        if (!empty($array) && !is_numeric(array_keys($array)[0])) {
            return array($array);
        }

        return $array;
    }

    /**
     * Get the config from the mapping file
     *
     * @param string $fileName
     */
    private function getConfigFromMappingFile($fileName)
    {
        $xml = file_get_contents($this->options['mapping-files'] . $fileName);

        $result = json_decode(json_encode(simplexml_load_string($xml)), true);

        $config = $result;

        return $config;
    }

    /**
     * Format the entity file name
     *
     * @param string $className
     * @return string
     */
    private function formatEntityFileName($className, $relativeNamespace)
    {
        $classNameParts = explode('\\', $className);

        return sprintf(
            '%s/%s/Abstract%s.php',
            rtrim($this->options['entity-files'], '/'),
            $relativeNamespace,
            array_pop($classNameParts)
        );
    }

    /**
     * Format the entity file name
     *
     * @param string $className
     * @return string
     */
    private function formatEntityConcreteFileName($className, $relativeNamespace)
    {
        $classNameParts = explode('\\', $className);

        return sprintf(
            '%s/%s/%s.php',
            rtrim($this->options['entity-files'], '/'),
            $relativeNamespace,
            array_pop($classNameParts)
        );
    }

    /**
     * Format unit test filename
     *
     * @param string $className
     * @return string
     */
    private function formatUnitTestFileName($className, $relativeNamespace)
    {
        $classNameParts = explode('\\', $className);

        return sprintf(
            '%s/%s/%sEntityTest.php',
            rtrim($this->options['test-files'], '/'),
            $relativeNamespace,
            array_pop($classNameParts)
        );
    }

    /**
     * Find all mapping files
     */
    private function findMappingFiles()
    {
        $this->respond('Iterating through mapping files in: ' . $this->options['mapping-files'], 'info');

        foreach (new DirectoryIterator($this->options['mapping-files']) as $fileInfo) {
            if ($fileInfo->isDot()) {
                continue;
            }

            $fileName = $fileInfo->getFilename();

            $key = '\\' . str_replace('.', '\\', str_replace('.dcm.xml', '', $fileName));

            $this->mappingFiles[$key] = $fileName;
        }

        if (empty($this->mappingFiles)) {
            $this->exitResponse('Error: mapping files were not created');
        }

        // @Note Sort the mapping files by key, as the OS may read them in a different order which will cause
        //   unnecessary changes
        ksort($this->mappingFiles, SORT_NATURAL);
    }

    /**
     * Describe table
     *
     * @param string $table
     * @return array
     */
    private function describeTable($table)
    {
        if (!isset($this->tableDescription[$table])) {
            $query = $this->pdo->prepare('DESC ' . $table);

            $query->execute();

            $this->tableDescription[$table] = $query->fetchAll(Pdo::FETCH_ASSOC);
        }

        return $this->tableDescription[$table];
    }

    /**
     * Get Table Comments
     *
     * @param string $table
     * @return array
     */
    private function getCommentsFromTable($table)
    {
        if (!isset($this->entityConfig[$table])) {
            return array();
        }

        return $this->entityConfig[$table];
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

    /**
     * Convert name to a readable format
     *
     * @param string $name
     * @return string
     */
    private function getReadableStringFromName($name)
    {
        $formatter = new CamelCaseToSeparator(' ');
        return ucfirst(strtolower($formatter->filter($name)));
    }

    protected function formatOptionValue($val)
    {
        if (is_numeric($val)) {
            return $val;
        }

        if (is_bool($val) && $val) {
            return 1;
        }

        if (is_bool($val) && !$val) {
            return 0;
        }

        return '"' . $val . '"';
    }

    /**
     * Generate the option string from the attributes
     *
     * @param array $attributes
     * @param string $which
     * @return string
     */
    private function generateOptionsFromAttributes($attributes, $which = 'column')
    {
        $options = array();

        foreach ($attributes as $key => $value) {
            if ($key === 'options') {
                $settings = [];

                foreach ($value as $label => $val) {
                    $settings[] = '"' . $label . '": ' . $this->formatOptionValue($val);
                }

                if (!empty($settings)) {
                    $options[] = 'options={' . implode(', ', $settings) . '}';
                }

                continue;
            }

            if (in_array($which, ['indexes', 'unique-constraints']) && $key == 'columns') {
                $value = explode(',', $value);
            }

            if (is_array($value)) {
                $values = array();

                foreach ($value as $index => $val) {
                    if (!is_numeric($index)) {
                        $values[] = sprintf('%s" = "%s', $index, $val);
                    } else {
                        $values[] = $val;
                    }
                }

                $value = implode('","', $values);
            }

            if (isset($this->optionFormat[$key][$which])) {
                $format = $this->optionFormat[$key][$which];
            } elseif (isset($this->optionFormat[$key]) && !is_array($this->optionFormat[$key])) {
                $format = $this->optionFormat[$key];
            } elseif (isset($this->optionFormat[$key]['column'])) {
                $format = $this->optionFormat[$key]['column'];
            } else {
                $format = $key . '=%s';
            }

            $string = sprintf($format, $value);

            if (!empty($string)) {
                $options[] = $string;
            }
        }

        $string = implode(', ', $options);

        if (strlen($string) > 80) {
            return implode(",\n     *     ", $options);
        }

        return $string;
    }

    /**
     * Map php types from doctrine type
     *
     * @param string $type
     * @return string
     */
    private function getPhpTypeFromType($type)
    {
        switch ($type) {
            case 'string':
            case 'boolean':
                return $type;
            case 'text':
            case 'yesno':
            case 'yesnonull':
            case 'encrypted_string':
                return 'string';
            case 'bigint':
            case 'integer':
            case 'smallint':
                return 'int';
            case 'datetime':
            case 'date':
                return '\DateTime';
            case 'decimal':
                return 'float';
            default:
                return 'unknown';
        }
    }
}

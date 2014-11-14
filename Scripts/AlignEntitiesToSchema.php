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

/**
 * Align Entities To Schema
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class AlignEntitiesToSchema
{
    const ENTITY_NAMESPACE = 'Olcs\\Db\\Entity\\';

    private $useTraits = false;

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
        'columns' => '
 *            columns={"%s"}',
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
            'manyToMany' => 'mappedBy="%ss"'
        ),
        'cascade' => 'cascade={"%s"}',
        'inversed-by' => array(
            'column' => 'inversedBy="%s"',
            'manyToOne' => 'inversedBy="%ss"',
            'manyToMany' => 'inversedBy="%ss"'
        ),
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

    /**
     * Initialise the variables
     */
    public function __construct()
    {
        chdir(__DIR__ . '/../');

        require_once(__DIR__ . '/../init_autoloader.php');

        $this->options = getopt(
            'u:p:d:',
            array(
                'help',
                'import-schema:',
                'mapping-files:',
                'entity-files:',
                'test-files:',
                'entity-config:'
            )
        );

        if (isset($this->options['help'])) {
            $this->exitResponse(
                'Usage \'php AlignEntitiesToSchema.php --import-schema /workspace/OLCS/olcs-backend/data/db/schema.sql '
                . '--mapping-files /workspace/OLCS/olcs-backend/data/mapping/ --entity-files '
                . '/workspace/OLCS/olcs-backend/module/Olcs/Db/src/Entity/ --test-files '
                . '/workspace/OLCS/olcs-backend/test/module/Olcs/Db/src/Entity/ --entity-config '
                . '/workspace/OLCS/olcs-backend/data/db/EntityConfig.php -uroot -ppassword -dolcs_be\''
            );
        }

        $this->checkForRequiredParams();
    }

    /**
     * Run the script
     */
    public function run()
    {
        $this->loadEntityConfig();

        $this->createDatabaseConnection();

        $this->maybeImportSchema();

        $this->removeOldMappingFiles();

        $this->generateNewMappingFiles();

        $this->removeOldEntities();

        $this->removeOldTraits();

        $this->findMappingFiles();

        $this->compileEntityConfig();

        $this->createTraits();

        $this->exchangeFieldsForTraits();

        $this->createEntities();

        $this->removeOldUnitTests();

        $this->createUnitTests();

        $this->importEntities();
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
        $this->respond('Generating new mapping files...', 'info');

        $command = '%s orm:convert-mapping --namespace="%s" --force --from-database xml %s';

        $pathToDoctrine = __DIR__ . '/../vendor/bin/doctrine-module';

        shell_exec(
            sprintf(
                $command,
                $pathToDoctrine,
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

        $entityDirectory = $this->options['entity-files'];

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

    private function getTraitLocation()
    {
        if ($this->useTraits) {
            return $this->options['entity-files'] . 'Traits/';
        } else {
            return __DIR__ . '/../data/Entity/Partials/';
        }
    }

    /**
     * Remove old traits
     */
    private function removeOldTraits()
    {
        $this->respond('Removing non-custom old traits ...', 'info');

        $entityDirectory = $this->getTraitLocation();

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

            $fields = $this->getFieldsFromConfig($config, $defaults, $comments, $camelCaseName, $nullables);

            $this->cacheFields($fields);

            $this->entities[$camelCaseName] = array(
                'name' => $camelCaseName,
                'softDeletable' => $this->hasSoftDeleteField($fields),
                'translatable' => $this->hasTranslatableField($fields),
                'className' => $config['entity']['@attributes']['name'],
                'table' => $config['entity']['@attributes']['table'],
                'ids' => $this->getIdsFromFields($fields),
                'indexes' => $this->getIndexesFromConfig($config),
                'unique-constraints' => $this->getUniqueConstraintsFromConfig($config),
                'traits' => array(
                    'CustomBaseEntity'
                ),
                'fields' => $fields,
                'hasCollections' => $this->getHasCollectionsFromConfig($config),
                'collections' => $this->getCollectionsFromConfig($config),
                'mappingFileName' => $fileName,
                'entityFileName' => $this->formatEntityFileName($className),
                'testFileName' => $this->formatUnitTestFileName($className)
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

                $property = $fieldDetails['relationship'] == 'oneToMany' ? 'mapped-by' : 'inversed-by';

                $item = array(
                    '@attributes' => array(
                        'field' => $fieldDetails['property'],
                        'target-entity' => $fieldDetails['targetEntity'],
                        $property => $fieldDetails['inversedBy']
                    ),
                    'orderBy' => $fieldDetails['orderBy']
                );

                $details['fields'][] = array(
                    'isId' => false,
                    'isInverse' => true,
                    'type' => $fieldDetails['relationship'],
                    'ref' => 'field',
                    'default' => null,
                    'config' => $item,
                );

                if ($fieldDetails['relationship'] == 'manyToMany' || $fieldDetails['relationship'] == 'oneToMany') {
                    $details['hasCollections'] = true;

                    $details['collections'][] = $item;
                }
            }
        }

        $this->respond('Entity configurations compiled', 'success');
    }

    /**
     * Get a list of id proeprties from the fields
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
     * Create the traits
     */
    private function createTraits()
    {
        $this->respond('Creating traits...', 'info');

        $error = false;

        foreach ($this->fields as $key => $encode) {
            $item = json_decode($encode, true);

            $field = $item['config'];

            $details = &$this->fieldDetails[$key];
            $which = $item['type'];

            switch ($which) {
                case 'id':
                    $description = 'Identity';
                    break;
                default:
                    $description = ucwords($which);
            }

            switch ($which) {
                case 'id':
                case 'field':
                    $ref = 'name';
                    break;
                default:
                    $ref = 'field';
            }

            if ($details['count'] < 2) {
                continue;
            }

            if (isset($field['@attributes']['type'])
                && $field['@attributes']['type'] == 'string'
                && isset($field['@attributes']['length'])) {
                $description = $field['@attributes']['length'] . $description;
            }

            $trait =
                ucwords((isset($item['property']) ? $item['property'] : $field['@attributes'][$ref])) . $description;

            $traitDirectory = $this->getTraitLocation();

            if ($this->useTraits) {
                $customFileName = sprintf('%s%s.php', $traitDirectory . 'Custom', $trait);

                // If we have a custom trait that already exists, skip it
                if (file_exists($customFileName)) {
                    $details['trait'] = 'Custom' . $trait;
                    $this->respond('Custom trait already exists: Custom' . $trait, 'warning');
                    continue;
                }
            } else {
                $customFileName = sprintf('%s%s_Methods.php', $traitDirectory . 'Custom', $trait);

                // If we have a custom trait that already exists, skip it
                if (file_exists($customFileName)) {
                    $details['trait'] = 'Custom' . $trait;
                    $this->respond('Custom trait already exists: Custom' . $trait, 'warning');
                    continue;
                }

                $customFileName = sprintf('%s%s_Properties.php', $traitDirectory . 'Custom', $trait);

                // If we have a custom trait that already exists, skip it
                if (file_exists($customFileName)) {
                    $details['trait'] = 'Custom' . $trait;
                    $this->respond('Custom trait already exists: Custom' . $trait, 'warning');
                    continue;
                }
            }

            $fileName = sprintf('%s%s', $traitDirectory, $trait);

            // If the trait file exists, we need to rename this one
            if (file_exists($fileName)) {

                $oldTrait = $trait;

                $i = 0;

                while (file_exists($fileName)) {

                    $i++;

                    $trait = $oldTrait . 'Alt' . $i;
                    $fileName = sprintf('%s%s', $traitDirectory, $trait);
                }

            }

            $details['trait'] = $trait;

            $fluidReturn = '\\Olcs\\Db\\Entity\\Interfaces\\EntityInterface';

            if ($this->useTraits) {

                ob_start();
                    include(__DIR__ . '/templates/trait.phtml');
                    $content = ob_get_contents();
                ob_end_clean();

                file_put_contents($fileName . '.php', $content);

                if (file_exists($fileName . '.php')) {
                    $this->respond('Trait created: ' . $trait);
                } else {
                    $error = true;
                    $this->respond('Trait creation failed: ' . $trait, 'error');
                }

            } else {

                ob_start();
                    echo "\n";
                    include(__DIR__ . '/templates/property.phtml');
                    echo "\n";
                    $content = ob_get_contents();
                ob_end_clean();

                file_put_contents($fileName . '_Properties.php', $content);

                ob_start();
                    echo "\n";
                    include(__DIR__ . '/templates/methods.phtml');
                    $content = ob_get_contents();
                ob_end_clean();

                file_put_contents($fileName . '_Methods.php', $content);

                if (file_exists($fileName . '_Properties.php') && file_exists($fileName . '_Methods.php')) {
                    $this->respond('Partial created: ' . $trait);
                } else {
                    $error = true;
                    $this->respond('Partial creation failed: ' . $trait, 'error');
                }
            }
        }

        if ($error) {
            $this->exitResponse('Some traits were unable to be created');
        } else {
            $this->respond('Traits created successfully', 'success');
        }
    }

    /**
     * Iterate through the entities to swap fields for traits
     */
    private function exchangeFieldsForTraits()
    {
        $this->respond('Exchanging fields for traits...', 'info');

        foreach ($this->entities as $className => &$details) {

            foreach ($details['fields'] as $index => $field) {

                $encode = json_encode($field);

                $key = array_search($encode, $this->fields);

                if ($key === false) {
                    continue;
                }

                if (is_null($this->fieldDetails[$key]['trait'])) {
                    continue;
                } else {
                    $details['traits'][] = $this->fieldDetails[$key]['trait'];
                    unset($details['fields'][$index]);
                }
            }

            $this->respond('Processed: ' . $className);
        }

        $this->respond('Fields exchanged for traits', 'success');
    }

    /**
     * Create entities
     */
    private function createEntities()
    {
        $this->respond('Creating entities...', 'info');

        $error = false;

        $traitDirectory = $this->getTraitLocation();

        foreach ($this->entities as $className => $details) {

            $details['propertyContent'] = '';
            $details['methodContent'] = '';

            if (!$this->useTraits) {
                foreach ($details['traits'] as $traitName) {
                    if (file_exists($traitDirectory . $traitName . '_Properties.php')) {
                        $details['propertyContent'] .= file_get_contents($traitDirectory . $traitName . '_Properties.php');
                    }

                    if (file_exists($traitDirectory . $traitName . '_Methods.php')) {
                        $details['methodContent'] .= file_get_contents($traitDirectory . $traitName . '_Methods.php');
                    }
                }

                $details['traits'] = array();
            }

            ob_start();
                include(__DIR__ . '/templates/entity.phtml');
                $content = ob_get_contents();
            ob_end_clean();

            file_put_contents($details['entityFileName'], $content);

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

    /**
     * Import entities
     */
    private function importEntities()
    {
        $this->respond('Importing entities...', 'info');

        $this->recreateDatabase();

        $output = shell_exec(realpath(__DIR__ . '/../vendor/bin/doctrine-module') . ' orm:schema:update --force');

        $this->respond($output, 'info');

        $this->respond('Entities imported', 'success');
    }

    /**
     * Remove old unit tests
     */
    private function removeOldUnitTests()
    {
        $this->respond('Removing old entity unit tests...', 'info');

        $directory = $this->options['test-files'];

        $error = false;

        foreach (new DirectoryIterator($directory) as $fileInfo) {

            if ($fileInfo->isDot() || $fileInfo->isDir()) {
                continue;
            }

            $fileName = $fileInfo->getFilename();

            unlink($directory . $fileName);
            if (!file_exists($directory . $fileName)) {
                $this->respond('Removed: ' . $fileName);
            } else {
                $error = true;
                $this->respond('Unable to remove: ' . $fileName, 'error');
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

            ob_start();
                include(__DIR__ . '/templates/test-entity.phtml');
                $content = ob_get_contents();
            ob_end_clean();

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
                    'count' => 0,
                    'trait' => null
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
                    'translatable' => false
                );

                if (isset($extraConfig['type'])) {
                    $fieldConfig['config']['@attributes']['type'] = $extraConfig['type'];
                    unset($extraConfig['type']);
                }

                $fields[] = array_merge($fieldConfig, $extraConfig);
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

                $fieldConfig = array(
                    'isId' => isset($associationKeys[$item['@attributes'][$key]]),
                    'isInverse' => false,
                    'type' => $which,
                    'ref' => ($which == 'field' ? 'name' : 'field'),
                    'default' => $default,
                    'config' => $item,
                    'translatable' => false
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
                        'orderBy' => isset($fieldConfig['inversedBy']['orderBy'])
                            ? $fieldConfig['inversedBy']['orderBy']
                            : null
                    );

                    $fieldConfig['config']['@attributes']['inversed-by'] = $fieldConfig['inversedBy']['property'];
                }

                $fields[] = $fieldConfig;
            }
        }

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
        }

        return $name;
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
    private function formatEntityFileName($className)
    {
        $classNameParts = explode('\\', $className);

        return sprintf(
            '%s/%s.php',
            rtrim($this->options['entity-files'], '/'),
            array_pop($classNameParts)
        );
    }

    /**
     * Format unit test filename
     *
     * @param string $className
     * @return string
     */
    private function formatUnitTestFileName($className)
    {
        $classNameParts = explode('\\', $className);

        return sprintf(
            '%s/%sTest.php',
            rtrim($this->options['test-files'], '/'),
            array_pop($classNameParts)
        );
    }

    /**
     * Find all mapping files
     */
    private function findMappingFiles()
    {
        foreach (new DirectoryIterator($this->options['mapping-files']) as $fileInfo) {
            if ($fileInfo->isDot()) {
                continue;
            }

            $fileName = $fileInfo->getFilename();

            $key = '\\' . str_replace('.', '\\', str_replace('.dcm.xml', '', $fileName));

            $this->mappingFiles[$key] = $fileName;
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

            if ($which == 'unique-constraints' && $key == 'columns') {
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

        return implode(', ', $options);
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

$cli = new AlignEntitiesToSchema();
$cli->run();

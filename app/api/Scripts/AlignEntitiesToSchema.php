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
     * Option format mapping
     *
     * @var array
     */
    private $optionFormat = array(
        'name' => array(
            'column' => '',
            'join-column' => 'name="%s"',
            'indexes' => 'name="%s"'
        ),
        'columns' => 'columns={"%s"}',
        'field' => '',
        'type' => 'type="%s"',
        'column' => 'name="%s"',
        'length' => 'length=%s',
        'nullable' => 'nullable=%s',
        'target-entity' => 'targetEntity="%s"',
        'referenced-column-name' => 'referencedColumnName="%s"',
        'mapped-by' => 'mappedBy="%s"',
        'cascade' => 'cascade={"%s"}',
        'inversed-by' => 'inversedBy="%s"'
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

            $dropDatabase = 'mysql -u%s -p%s -e \'DROP DATABASE IF EXISTS %s\'';

            $createDatabase = 'mysql -u%s -p%s -e \'CREATE DATABASE IF NOT EXISTS %s\'';

            $importSchemaCommand = 'mysql -u%s -p%s < %s';

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

            shell_exec(
                sprintf(
                    $importSchemaCommand,
                    $this->options['u'],
                    $this->options['p'],
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
        $this->respond('Compiling entity configuration...', 'info');

        foreach ($this->mappingFiles as $className => $fileName) {

            $config = $this->getConfigFromMappingFile($fileName);

            $defaults = $this->getDefaultsFromTable($config['entity']['@attributes']['table']);

            $fields = $this->getFieldsFromConfig($config, $defaults);

            $this->cacheFields($fields);

            $this->entities[$className] = array(
                'name' => str_replace(self::ENTITY_NAMESPACE, '', $config['entity']['@attributes']['name']),
                'table' => $config['entity']['@attributes']['table'],
                'indexes' => $this->getIndexesFromConfig($config),
                'traits' => array(
                    'CustomBaseEntity'
                ),
                'fields' => $fields,
                'hasCollections' => $this->getHasCollectionsFromConfig($config),
                'mappingFileName' => $fileName,
                'entityFileName' => $this->formatEntityFileName($className),
            );
        }

        $this->respond('Entity configurations compiled', 'success');
    }

    /**
     * Create the traits
     */
    private function createTraits()
    {
        $this->respond('Creating traits...', 'info');

        $error = false;

        foreach ($this->fields as $key => $encode)
        {
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

            $trait = ucwords($field['@attributes'][$ref]) . $description;

            $fileName = sprintf('%s%s.php', $this->options['entity-files'] . 'OlcsEntities/Entity/Traits/', $trait);
            $customFileName = sprintf('%s%s.php', $this->options['entity-files'] . 'OlcsEntities/Entity/Traits/Custom', $trait);

            // If we have a custom trait that already exists, skip it
            if (file_exists($customFileName)) {
                $details['trait'] = 'Custom' . $trait;
                $this->respond('Custom trait already exists: Custom' . $trait, 'warning');
                continue;
            }

            $details['trait'] = $trait;

            $fluidReturn = '\\OlcsEntities\\Entity\\Interfaces\\EntityInterface';

            ob_start();
                include(__DIR__ . '/templates/trait.phtml');
                $content = ob_get_contents();
            ob_end_clean();

            file_put_contents($fileName, $content);

            if (file_exists($fileName)) {
                $this->respond('Trait created: ' . $trait);
            } else {
                $error = true;
                $this->respond('Trait creation failed: ' . $trait, 'error');
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

        foreach ($this->entities as $className => $details) {

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
        return (int)(isset($config['entity']['manyToMany']) || isset($config['entity']['oneToMany']));
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
     * Get fields form config
     *
     * @param array $config
     * @param array $defaults
     * @return array
     */
    private function getFieldsFromConfig($config, $defaults)
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

                if (!isset($defaults[$this->formatColumnFromName($item['@attributes']['name'])])) {
                    $default = null;
                } else {
                    $default = $defaults[$this->formatColumnFromName($item['@attributes']['name'])];
                }

                $fields[] = array(
                    'isId' => true,
                    'type' => 'id',
                    'ref' => 'name',
                    'default' => $default,
                    'config' => $item,
                );
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

                if (!isset($defaults[$this->formatColumnFromName($item['@attributes'][$key])])) {
                    $default = null;
                } else {
                    $default = $defaults[$this->formatColumnFromName($item['@attributes'][$key])];
                }

                $fields[] = array(
                    'isId' => isset($associationKeys[$item['@attributes'][$key]]),
                    'type' => $which,
                    'ref' => ($which == 'field' ? 'name' : 'field'),
                    'default' => $default,
                    'config' => $item
                );
            }
        }

        return $fields;
    }

    /**
     * Format column name from name
     *
     * @param string $name
     * @return string
     */
    private function formatColumnFromName($name)
    {
        $filter = new CamelCaseToSeparator('_');

        return strtolower($filter->filter($name));
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
        return sprintf(
            '%s%s.php',
            rtrim($this->options['entity-files'], '/'),
            str_replace('\\', '/', $className)
        );
    }

    /**
     * Find all mapping files
     */
    private function findMappingFiles()
    {
        foreach (new DirectoryIterator($this->options['mapping-files']) as $fileInfo) {
            if($fileInfo->isDot()) {
                continue;
            }

            $fileName = $fileInfo->getFilename();

            $key = '\\' . str_replace('.', '\\', str_replace('.dcm.xml', '', $fileName));

            $this->mappingFiles[$key] = $fileName;
        }
    }

    /**
     * Describe table
     *
     * @param string $table
     * @return array
     */
    private function describeTable($table)
    {
        $query = $this->pdo->prepare('DESC ' . $table);

        $query->execute();

        return $query->fetchAll(Pdo::FETCH_ASSOC);
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

            if ($key == 'type') {
                $value = $this->getDoctrineTypeFromType($value, $attributes);
            }

            if (is_array($value)) {
                $value = implode('","', $value);
            }

            if (isset($this->optionFormat[$key][$which])) {
                $format = $this->optionFormat[$key][$which];
            } elseif (isset($this->optionFormat[$key])) {
                $format = $this->optionFormat[$key];
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
     * Map custom doctrine type from types
     *
     * @param string $type
     * @param array $context
     * @return string
     */
    private function getDoctrineTypeFromType($type, $context = array())
    {
        switch ($type) {
            case 'boolean':

                if (!isset($context['nullable']) || $context['nullable'] == false) {
                    return 'yesno';
                }

                return 'yesnonull';
            default:
                return $type;
        }
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

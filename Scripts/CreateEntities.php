<?php

namespace Cli;

use DirectoryIterator;
use Zend\Filter\Word\CamelCaseToSeparator;

chdir(__DIR__ . '/../');

/**
 * Create enttities from Doctrines mapping files
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class CreateEntities
{
    /**
     * Holds the mapping directory
     *
     * @var string
     */
    private $mappingDirectory;

    /**
     * Holds the entity directory
     *
     * @var string
     */
    private $entityFolder;

    /**
     * Holds the namespace
     *
     * @var string
     */
    private $namespace;

    /**
     * Holds the mapping files
     *
     * @var array
     */
    private $mappingFiles = array();

    /**
     * Holds the new classes
     *
     * @var array
     */
    private $newClasses = array();

    /**
     * Holds the existing classes
     *
     * @var array
     */
    private $existingClasses = array();

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
     * Cache for trait creation
     *
     * @var array
     */
    private $id = array();
    private $idDetails = array();
    private $field = array();
    private $fieldDetails = array();
    private $manyToOne = array();
    private $manyToOneDetails = array();
    private $manyToMany = array();
    private $manyToManyDetails = array();
    private $oneToMany = array();
    private $oneToManyDetails = array();
    private $oneToOne = array();
    private $oneToOneDetails = array();

    /**
     * Holds the entity configs
     *
     * @var array
     */
    private $entityConfigs = array();

    /**
     * Holds the DB connection
     *
     * @var \Pdo
     */
    private $pdo;

    /**
     * Init the vars
     */
    public function __construct()
    {
        require_once(__DIR__ . '/../init_autoloader.php');

        $this->mappingDirectory = __DIR__ . '/../data/mapping/';

        $this->entityFolder = __DIR__ . '/../../olcs-entities/OlcsEntities/src/';

        $this->namespace = 'OlcsEntities\\Entity';

        $this->pdo = new \Pdo('mysql:dbname=olcs;host=localhost', 'olcs_be', 'password');
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

        return $query->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Run the CLI program
     */
    public function run()
    {
        $this->findMappingFiles();

        $this->findNewAndOldEntities();

        $this->createEntities();
    }

    /**
     * Create entities
     */
    private function createEntities()
    {
        $entities = array_merge($this->newClasses, $this->existingClasses);

        $this->cacheComponents($entities);

        $this->createTraits();

        foreach ($entities as $className) {

            $fileName = sprintf('%s%s.php', $this->entityFolder, str_replace('\\', '/', $className));

            $content = $this->createEntityTemplate($this->mappingFiles[$className]);

            file_put_contents($fileName, $content);
        }
    }

    /**
     * Create the entity template content
     *
     * @param string $mappingFile
     * @return string
     */
    private function createEntityTemplate($mappingFile)
    {
        $config = $this->getConfigFromMappingFile($mappingFile);

        $ids = array();

        if (isset($config['entity']['id'])) {

            if (!is_numeric(array_keys($config['entity']['id'])[0])) {
                $ids[$config['entity']['id']['@attributes']['name']] = $config['entity']['id'];
            } else {
                foreach ($config['entity']['id'] as $id) {
                    $ids[$id['@attributes']['name']] = $id;
                }
            }
        }

        $tableDescription = $this->describeTable($config['entity']['@attributes']['table']);

        $defaults = array();

        foreach ($tableDescription as $row) {
            $defaults[$row['Field']] = $row['Default'];
        }

        $manyToOne = $this->standardiseArray(isset($config['entity']['many-to-one']) ? $config['entity']['many-to-one'] : array());
        $manyToMany = $this->standardiseArray(isset($config['entity']['many-to-many']) ? $config['entity']['many-to-many'] : array());
        $oneToMany = $this->standardiseArray(isset($config['entity']['one-to-many']) ? $config['entity']['one-to-many'] : array());
        $oneToOne = $this->standardiseArray(isset($config['entity']['one-to-one']) ? $config['entity']['one-to-one'] : array());
        $indexes = $this->standardiseArray(isset($config['entity']['indexes']['index']) ? $config['entity']['indexes']['index'] : array());
        $fields = $this->standardiseArray(isset($config['entity']['field']) ? $config['entity']['field'] : array());

        $constructCollectionProperties = array_merge($manyToMany, $oneToMany);

        $traits = array(
            'CustomBaseEntity'
        );

        foreach ($ids as $k => $item) {
            $key = $this->getCacheKey($item, 'id');

            if ($key !== false && $this->idDetails[$key]['trait'] !== null) {
                $traits[] = $this->idDetails[$key]['trait'];
                unset($ids[$k]);
            }
        }

        foreach ($fields as $k => $item) {
            $key = $this->getCacheKey($item, 'field');

            if ($key !== false && $this->fieldDetails[$key]['trait'] !== null) {
                $traits[] = $this->fieldDetails[$key]['trait'];
                unset($fields[$k]);
            }
        }

        foreach ($manyToOne as $k => $item) {
            $key = $this->getCacheKey($item, 'manyToOne');

            if ($key !== false && $this->manyToOneDetails[$key]['trait'] !== null) {
                $traits[] = $this->manyToOneDetails[$key]['trait'];
                unset($manyToOne[$k]);
            }
        }

        foreach ($manyToMany as $k => $item) {
            $key = $this->getCacheKey($item, 'manyToMany');

            if ($key !== false && $this->manyToManyDetails[$key]['trait'] !== null) {
                $traits[] = $this->manyToManyDetails[$key]['trait'];
                unset($manyToMany[$k]);
            }
        }

        foreach ($oneToOne as $k => $item) {
            $key = $this->getCacheKey($item, 'oneToOne');

            if ($key !== false && $this->oneToOneDetails[$key]['trait'] !== null) {
                $traits[] = $this->oneToOneDetails[$key]['trait'];
                unset($oneToOne[$k]);
            }
        }

        foreach ($oneToMany as $k => $item) {
            $key = $this->getCacheKey($item, 'oneToMany');

            if ($key !== false && $this->oneToManyDetails[$key]['trait'] !== null) {
                $traits[] = $this->oneToManyDetails[$key]['trait'];
                unset($oneToMany[$k]);
            }
        }

        $settersAndGetters = array_merge($manyToOne, $oneToOne);
        $collectionProperties = array_merge($manyToMany, $oneToMany);

        ob_start();
            include(__DIR__ . '/templates/NewEntity.phtml');
            $content = ob_get_contents();
        ob_end_clean();

        return $content;
    }

    /**
     * Cache components
     *
     * @param array $entities
     */
    private function cacheComponents($entities)
    {
        foreach ($entities as $className) {

            $config = $this->getConfigFromMappingFile($this->mappingFiles[$className]);

            $ids = array();
            if (isset($config['entity']['id'])) {

                if (!is_numeric(array_keys($config['entity']['id'])[0])) {
                    $ids[$config['entity']['id']['@attributes']['name']] = $config['entity']['id'];
                } else {
                    foreach ($config['entity']['id'] as $id) {
                        $ids[$id['@attributes']['name']] = $id;
                    }
                }
            }

            $manyToOne = $this->standardiseArray(isset($config['entity']['many-to-one']) ? $config['entity']['many-to-one'] : array());
            $manyToMany = $this->standardiseArray(isset($config['entity']['many-to-many']) ? $config['entity']['many-to-many'] : array());
            $oneToMany = $this->standardiseArray(isset($config['entity']['one-to-many']) ? $config['entity']['one-to-many'] : array());
            $oneToOne = $this->standardiseArray(isset($config['entity']['one-to-one']) ? $config['entity']['one-to-one'] : array());
            $fields = $this->standardiseArray(isset($config['entity']['field']) ? $config['entity']['field'] : array());

            $idFkAndPk = array();

            foreach ($ids as $item) {
                if (isset($item['@attributes']['association-key'])) {
                    $idFkAndPk[] = $item['@attributes']['name'];
                    continue;
                }
                $this->cacheComponent($item, 'id');
            }

            foreach ($fields as $item) {
                if (in_array($item['@attributes']['name'], $idFkAndPk)) {
                    continue;
                }
                $this->cacheComponent($item, 'field');
            }

            foreach ($manyToOne as $item) {
                if (in_array($item['@attributes']['field'], $idFkAndPk)) {
                    continue;
                }
                $this->cacheComponent($item, 'manyToOne');
            }

            foreach ($manyToMany as $item) {
                if (in_array($item['@attributes']['field'], $idFkAndPk)) {
                    continue;
                }
                $this->cacheComponent($item, 'manyToMany');
            }

            foreach ($oneToMany as $item) {
                if (in_array($item['@attributes']['field'], $idFkAndPk)) {
                    continue;
                }
                $this->cacheComponent($item, 'oneToMany');
            }

            foreach ($oneToOne as $item) {
                if (in_array($item['@attributes']['field'], $idFkAndPk)) {
                    continue;
                }
                $this->cacheComponent($item, 'oneToOne');
            }
        }
    }

    /**
     * Cache a component and count the number of uses
     *
     * @param array $item
     * @param string $which
     */
    private function cacheComponent($item, $which)
    {
        $encode = json_encode($item);

        $key = array_search($encode, $this->{$which});

        if ($key === false) {
            $this->{$which}[] = $encode;
            $this->{$which . 'Details'}[] = array(
                'count' => 0,
                'trait' => null
            );

            $key = count($this->$which) - 1;
        }

        $this->{$which . 'Details'}[$key]['count']++;
    }

    private function getCacheKey($item, $which)
    {
        $encode = json_encode($item);

        return array_search($encode, $this->{$which});
    }

    /**
     * For each component we need to create a trait
     */
    private function createTraits()
    {
        $this->createComponentTraits('id');
        $this->createComponentTraits('field');
        $this->createComponentTraits('manyToOne');
        $this->createComponentTraits('manyToMany');
        $this->createComponentTraits('oneToOne');
        $this->createComponentTraits('oneToMany');
    }

    /**
     * Create the ID Traits
     */
    private function createComponentTraits($which)
    {
        foreach ($this->{$which} as $key => $encode)
        {
            $details = &$this->{$which . 'Details'}[$key];

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

            $data = json_decode($encode, true);

            if (isset($data['@attributes']['type'])
                && $data['@attributes']['type'] == 'string'
                && isset($data['@attributes']['length'])) {
                $description = $data['@attributes']['length'] . $description;
            }

            $trait = ucwords($data['@attributes'][$ref]) . $description;

            $fileName = sprintf('%s%s.php', $this->entityFolder . 'OlcsEntities/Entity/Traits/', $trait);
            $customFileName = sprintf('%s%s.php', $this->entityFolder . 'OlcsEntities/Entity/Traits/Custom', $trait);

            // If we have a custom trait that already exists
            if (file_exists($customFileName)) {
                $details['trait'] = 'Custom' . $trait;
                continue;
            }

            $details['trait'] = $trait;

            ob_start();
                include(__DIR__ . '/templates/trait-' . $which . '.phtml');
                $content = ob_get_contents();
            ob_end_clean();

            file_put_contents($fileName, $content);
        }
    }

    /**
     * Get the config from the mapping file
     *
     * @param string $mappingFile
     * @return array
     */
    private function getConfigFromMappingFile($mappingFile)
    {
        $xml = file_get_contents($this->mappingDirectory . $mappingFile);

        return $this->convertXmlToArray($xml);
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

    /**
     * Convert XML to array
     *
     * @param string $xml
     * @return array
     */
    private function convertXmlToArray($xml)
    {
        $result = json_decode(json_encode(simplexml_load_string($xml)), true);

        $config = $result;

        $config['name'] = str_replace($this->namespace . '\\', '', $result['entity']['@attributes']['name']);

        return $config;
    }

    /**
     * Find all mapping files
     */
    private function findMappingFiles()
    {
        foreach (new DirectoryIterator($this->mappingDirectory) as $fileInfo) {
            if($fileInfo->isDot()) {
                continue;
            }

            $fileName = $fileInfo->getFilename();

            $key = '\\' . str_replace('.', '\\', str_replace('.dcm.xml', '', $fileName));

            $this->mappingFiles[$key] = $fileName;
        }
    }

    /**
     * Distinguish between new and old entities
     */
    private function findNewAndOldEntities()
    {
        foreach (array_keys($this->mappingFiles) as $className) {

            if (class_exists($className)) {
                $this->existingClasses[] = $className;
            } else {
                $this->newClasses[] = $className;
            }
        }
    }
}

$runner = new CreateEntities();
$runner->run();

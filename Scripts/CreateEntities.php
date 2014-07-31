<?php

// /workspace/OLCS/olcs-backend/vendor/bin/doctrine-module orm:convert-mapping --namespace="OlcsEntities\\Entity\\" --force --from-database xml /workspace/OLCS/olcs-backend/data/mapping/
// /workspace/OLCS/olcs-backend/vendor/bin/doctrine-module orm:generate-entities --generate-annotations true /workspace/OLCS/olcs-backend/data/entities/

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
        'mapped-by' => 'mapperBy="%s"',
        'cascade' => 'cascade={"%s"}',
        'inversed-by' => 'inversedBy="%s"'
    );

    /**
     * Init the vars
     */
    public function __construct()
    {
        require_once(__DIR__ . '/../init_autoloader.php');

        $this->mappingDirectory = __DIR__ . '/../data/mapping/';

        $this->entityFolder = __DIR__ . '/../../olcs-entities/OlcsEntities/src/';

        $this->namespace = 'OlcsEntities\\Entity';
    }

    /**
     * Run the CLI program
     */
    public function run()
    {
        $this->findMappingFiles();

        $this->findNewAndOldEntities();

        $this->createNewEntities();
    }

    /**
     * Create new entities
     */
    private function createNewEntities()
    {
        foreach ($this->newClasses as $className) {

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

        $manyToOne = $this->standardiseArray(isset($config['entity']['many-to-one']) ? $config['entity']['many-to-one'] : array());
        $manyToMany = $this->standardiseArray(isset($config['entity']['many-to-many']) ? $config['entity']['many-to-many'] : array());
        $oneToMany = $this->standardiseArray(isset($config['entity']['one-to-many']) ? $config['entity']['one-to-many'] : array());
        $oneToOne = $this->standardiseArray(isset($config['entity']['one-to-one']) ? $config['entity']['one-to-one'] : array());
        $indexes = $this->standardiseArray(isset($config['entity']['indexes']['index']) ? $config['entity']['indexes']['index'] : array());
        $fields = $this->standardiseArray(isset($config['entity']['field']) ? $config['entity']['field'] : array());

        $settersAndGetters = array_merge($manyToOne, $oneToOne);
        $collectionProperties = array_merge($manyToMany, $oneToMany);

        ob_start();
            include(__DIR__ . '/templates/NewEntity.phtml');
            $content = ob_get_contents();
        ob_end_clean();

        return $content;
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

                if (isset($context['nullable']) && $context['nullable'] == false) {
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

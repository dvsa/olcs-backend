<?php

// /workspace/OLCS/olcs-backend/vendor/bin/doctrine-module orm:convert-mapping --namespace="OlcsEntities\\Entity\\" --force --from-database xml /workspace/OLCS/olcs-backend/data/mapping/
// /workspace/OLCS/olcs-backend/vendor/bin/doctrine-module orm:generate-entities --generate-annotations true /workspace/OLCS/olcs-backend/data/entities/

namespace Cli;

use DirectoryIterator;
use Zend\Filter\Word\CamelCaseToSeparator;

chdir(__DIR__ . '/../');

class CreateEntities
{
    private $mappingDirectory;

    private $entityFolder;

    private $namespace;

    private $mappingFiles = array();

    private $newClasses = array();

    private $existingClasses = array();

    private $optionFormat = array(
        'name' => array(
            'column' => '',
            'join-column' => 'name="%s"'
        ),
        'field' => '',
        'type' => 'type="%s"',
        'column' => 'name="%s"',
        'length' => 'length=%s',
        'nullable' => 'nullable=%s',
        'target-entity' => 'targetEntity="%s"',
        'referenced-column-name' => 'referencedColumnName="%s"',
        'mapped-by' => 'mapperBy="%s"'
    );

    public function __construct()
    {
        require_once(__DIR__ . '/../init_autoloader.php');

        $this->mappingDirectory = __DIR__ . '/../data/mapping/';

        $this->entityFolder = __DIR__ . '/../../olcs-entities/OlcsEntities/src/';

        $this->namespace = 'OlcsEntities\\Entity';
    }

    public function run()
    {
        $this->findMappingFiles();

        $this->findNewAndOldEntities();

        $this->createNewEntities();
    }

    private function createNewEntities()
    {
        foreach ($this->newClasses as $className) {

            $fileName = sprintf('%s%s.php', $this->entityFolder, str_replace('\\', '/', $className));

            $content = $this->createEntityTemplate($this->mappingFiles[$className]);
        }
    }

    private function createEntityTemplate($mappingFile)
    {
        $xml = file_get_contents($this->mappingDirectory . $mappingFile);

        $config = $this->convertXmlToArray($xml);
        $manyToOne = isset($config['entity']['many-to-one']) ? $config['entity']['many-to-one'] : array();
        $manyToMany = isset($config['entity']['many-to-many']) ? $config['entity']['many-to-many'] : array();
        $oneToMany = isset($config['entity']['one-to-many']) ? $config['entity']['one-to-many'] : array();
        $oneToOne = isset($config['entity']['one-to-one']) ? $config['entity']['one-to-one'] : array();

        if (!empty($manyToOne) && !is_numeric(array_keys($manyToOne)[0])) {
            $manyToOne = array($manyToOne);
        }

        if (!empty($manyToMany) && !is_numeric(array_keys($manyToMany)[0])) {
            $manyToMany = array($manyToMany);
        }

        if (!empty($oneToMany) && !is_numeric(array_keys($oneToMany)[0])) {
            $oneToMany = array($oneToMany);
        }

        if (!empty($oneToOne) && !is_numeric(array_keys($oneToOne)[0])) {
            $oneToOne = array($oneToOne);
        }

        if (!isset($config['entity']['one-to-one'])) {
            return;
        }

        print_r($config);
        //exit;

        ob_start();
            include(__DIR__ . '/templates/NewEntity.phtml');
            $content = ob_get_contents();
        ob_end_clean();

        die($content);

        return $content;
    }

    private function getReadableStringFromName($name)
    {
        $formatter = new CamelCaseToSeparator(' ');
        return ucfirst(strtolower($formatter->filter($name)));
    }

    private function generateOptionsFromAttributes($attributes, $which = 'column')
    {
        $options = array();

        foreach ($attributes as $key => $value) {

            $format = isset($this->optionFormat[$key][$which])
                ? $this->optionFormat[$key][$which] : $this->optionFormat[$key];

            $string = sprintf($format, $value);

            if (!empty($string)) {
                $options[] = $string;
            }
        }

        return implode(', ', $options);
    }

    private function getPhpTypeFromType($type)
    {
        switch ($type) {
            case 'string':
                return $type;
            case 'integer':
                return 'int';
            default:
                return 'unknown';
        }
    }

    private function convertXmlToArray($xml)
    {
        $result = json_decode(json_encode(simplexml_load_string($xml)), true);

        $config = $result;

        $config['name'] = str_replace($this->namespace . '\\', '', $result['entity']['@attributes']['name']);

        return $config;
    }

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

    private function findNewAndOldEntities()
    {
        foreach ($this->mappingFiles as $className => $fileName) {

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
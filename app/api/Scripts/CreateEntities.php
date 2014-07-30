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
        'name' => '',
        'type' => 'type="%s"',
        'column' => 'name="%s"',
        'length' => 'length=%s',
        'nullable' => 'nullable=%s'
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

        print_r($config);
        exit;

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

    private function generateOptionsFromAttributes($attributes)
    {
        $options = array();

        foreach ($attributes as $key => $value) {
            $string = sprintf($this->optionFormat[$key], $value);

            if (!empty($string)) {
                $options[] = sprintf($this->optionFormat[$key], $value);
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
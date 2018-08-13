<?php

namespace Dvsa\Olcs\Cli\Domain\QueryHandler\Util;

use Dvsa\Olcs\Api\Domain\Exception\RuntimeException;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Domain\QueryHandler\Result;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RecursiveRegexIterator;
use RegexIterator;

class getDbValue extends AbstractQueryHandler
{

    private $entity = null;

    protected $repoServiceName;

    /**
     * Handle query
     *
     * @param \Dvsa\Olcs\Cli\Domain\Query\Util\getDbValue $query query
     *
     * @return Result
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     */
    public function handleQuery(QueryInterface $query)
    {
        $result = new Result();

        $this->repoServiceName = $query->getTableName();
        if ($this->isValidEntity() && $this->isValidProperty($query, $query->getColumnName())) {
            return $result;
        }
    }


    private function isValidEntity(): bool
    {
        $this->entity = $this->getEntityFromName($this->repoServiceName);
        return !empty($this->entity);
    }

    private function isValidProperty(QueryInterface $query, $property): bool
    {

        try {
            $entity = $this->getRepo($this->repoServiceName)->fetchList()[0];
            return property_exists($entity, $property);
        } catch (RuntimeException $runtimeException) {
            throw $runtimeException;
        }
    }

    protected function getEntityFromName(string $tableName)
    {
        $fqdn = [];
        $Directory = new RecursiveDirectoryIterator(__DIR__ . '/../../../../../Api/src/Entity');
        $Iterator = new RecursiveIteratorIterator($Directory);
        $regex = '/^(.+\/' . preg_quote($this->repoServiceName) . '\.php)$/m';
        $matches = new RegexIterator($Iterator, $regex);

        foreach ($matches as $file) {
            $fileContents = file_get_contents($file);
            $fqdn [] = $this->getFqdn($fileContents) . '\\' . filter_var(
                    $this->repoServiceName,
                    FILTER_SANITIZE_STRING
                );
        }

        if (count($fqdn) <= 1 && class_exists($fqdn[0])) {
            return new $fqdn[0];
        }
    }

    protected function getFqdn(string $contents)
    {
        $namespace = '';
        $isNamespace = false;
        //Go through each token and evaluate it as necessary
        foreach (token_get_all($contents) as $token) {
            if (is_array($token) && $token[0] == T_NAMESPACE) {
                $isNamespace = true;
            }
            if ($isNamespace) {
                if (is_array($token) && in_array($token[0], [T_STRING, T_NS_SEPARATOR])) {
                    $namespace .= $token[1];
                } else {
                    if ($token === ';') {
                        break;
                    }
                }
            }
        }
        return $namespace;
    }
}



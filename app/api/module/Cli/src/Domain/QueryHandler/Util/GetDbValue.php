<?php

namespace Dvsa\Olcs\Cli\Domain\QueryHandler\Util;

use Doctrine\ORM\Mapping\Entity;
use Dvsa\Olcs\Api\Domain\Exception\NotFoundException;
use Dvsa\Olcs\Api\Domain\Exception\RuntimeException;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Domain\QueryHandler\Result;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RecursiveRegexIterator;
use ReflectionClass;
use RegexIterator;
use DVSA\Olcs\Api\Domain\Repository\GetDbValue as GetDbValueRepo;

class GetDbValue extends AbstractQueryHandler
{

    private $entity = null;


    protected $repoServiceName = 'GetDbValue';

    private $entityName;


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

        $this->entityName = $query->getTableName();
        if ($this->isValidEntity() && $this->isValidProperty($query, $query->getColumnName())) {

            /** @var GetDbValueRepo $repo */
            $repo = $this->setEntityOnRepo();

            try {
                $data = $repo->fetchOneEntityByX($query->getFilterName(), $query->getFilterValue());
                $result->serialize($data);
            } catch (NotFoundException $notFoundException) {
                $result->setValue("error", "Not found");
            }

            return $result;
        }
    }


    private function isValidEntity(): bool
    {
        $this->entity = $this->getEntityFromName($this->entityName);
        return !empty($this->entity);
    }

    private function isValidProperty(QueryInterface $query, $property): bool
    {
        try {
            $entity = $this->setEntityOnRepo()->fetchList($query)[0];
            return property_exists($entity, $property);
        } catch (RuntimeException $runtimeException) {
            throw $runtimeException;
        }
    }

    protected function getEntityFromName(string $tableName = null)
    {
        $fqdn = [];
        $Directory = new RecursiveDirectoryIterator(__DIR__ . '/../../../../../Api/src/Entity');
        $Iterator = new RecursiveIteratorIterator($Directory);
        $regex = '/^(.+\/' . preg_quote($this->entityName) . '\.php)$/m';
        $matches = new RegexIterator($Iterator, $regex);

        foreach ($matches as $file) {
            $fileContents = file_get_contents($file);
            $fqdn [] = $this->getFqdn($fileContents) . '\\' . filter_var(
                    $this->entityName,
                    FILTER_SANITIZE_STRING
                );
        }

        if (count($fqdn) === 1 && class_exists($fqdn[0])) {
            return $fqdn[0];
        } else {
            foreach ($fqdn as $entityClass) {
                //check for table_name in docComment
                $class = new ReflectionClass($entityClass);
                $comment = $class->getDocComment();
                $matches = [];
                if (preg_match_all('/@ORM\\Table\(name=\"(.*)\"/', $comment, $matches)) {
                    if ($matches[1] === $tableName) {
                        return $entityClass;
                    }
                };


            }
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

    /**
     * setEntityOnRepo
     *
     * @return \Dvsa\Olcs\Api\Domain\Repository\RepositoryInterface
     * @throws RuntimeException
     */
    private function setEntityOnRepo(): \Dvsa\Olcs\Api\Domain\Repository\RepositoryInterface
    {
        $repo = $this->getRepo();
        $repo->setEntity($this->entity);
        return $repo;
    }
}



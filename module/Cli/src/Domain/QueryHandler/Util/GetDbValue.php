<?php

namespace Dvsa\Olcs\Cli\Domain\QueryHandler\Util;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Domain\QueryHandler\Result;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

class getDbValue extends AbstractQueryHandler
{

    private $fqdn = 'Dvsa\\Olcs\\Api\Entity\\';

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
        if ($this->isValidEntity() && $this->isValidProperty($query->getColumnName())) {
            return $result;
        }
    }


    private function isValidEntity(): bool
    {
        return class_exists($this->fqdn . $this->repoServiceName);
    }

    private function isValidProperty(string $property): bool
    {
        $className = $this->fqdn . filter_var($this->repoServiceName, FILTER_SANITIZE_STRING);
        $entity = new $className();
        return property_exists($entity, $property);
    }
}

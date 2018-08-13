<?php

namespace Dvsa\Olcs\Cli\Domain\QueryHandler\Util;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

class getDbValue extends AbstractQueryHandler
{
    /**
     * Handle query
     *
     * @param \Dvsa\Olcs\Cli\Domain\Query\Util\getDbValue $query query
     *
     * @return array
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     */
    private $fqdn = 'Dvsa\\Olcs\\Api\Entity\\';

    public function handleQuery(QueryInterface $query)
    {

        protected $repoServiceName = $query->getTableName();
        if($this->isValidEntity() && $this->isValidProperty($query->))
    }


    private function isValidEntity() : bool
    {
        return class_exists($this->fqdn .$this->repoServiceName);
    }

    private function isValidProperty(string $property) : bool
    {
        $className = $this->fqdn.filter_var($this->repoServiceName,FILTER_SANITIZE_STRING);

        $entity = new $className();
        return property_exists($entity, $property);
    }




}
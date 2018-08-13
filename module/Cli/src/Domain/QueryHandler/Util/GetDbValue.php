<?php

namespace Dvsa\Olcs\Cli\Domain\QueryHandler\Util;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Domain\QueryHandler\Result;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use DVSA\Olcs\Api\Domain\Repository\GetDbValue as GetDbValueRepo;

class getDbValue extends AbstractQueryHandler
{

    private $fqdn = 'Dvsa\\Olcs\\Api\Entity\\';

    protected $repoServiceName = 'GetDbValue';

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

        /** @var GetDbValueRepo $repo */
        $repo = $this->getRepo();
        $repo->setEntity('Dvsa\\Olcs\\Api\\Entity\\Licence\\Licence');

        $entity = $repo->fetchOneEntityByX($query->getFilterName(),$query->getFilterValue());



        $value = call_user_func(['get' . $query->getColumnName(), $entity]);

        return $value;

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

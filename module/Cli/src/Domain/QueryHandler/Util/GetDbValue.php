<?php

namespace Dvsa\Olcs\Cli\Domain\QueryHandler\Util;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Cli\Domain\Query\Util\GetDbValue as GetDbValueQuery;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Domain\QueryHandler\Result;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use DVSA\Olcs\Api\Domain\Repository\GetDbValue as GetDbValueRepo;

class GetDbValue extends AbstractQueryHandler
{
    const ENTITIES_NAMESPACE = '\Dvsa\Olcs\Api\Entity\\';

    private $entityName;

    protected $repoServiceName = 'GetDbValue';

    private $requiredParameters = ['property-name', 'entity-name' , 'filter-property', 'filter-value',];

    /**
     * @param GetDbValueQuery $query
     */
    private function setEntityNameFromQuery(QueryInterface $query): void
    {
        $this->entityName = self::ENTITIES_NAMESPACE . $query->getEntityName();
    }

    /**
     * Handle query
     *
     * @param GetDbValueQuery $query query
     *
     * @return Result|false
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     */
    public function handleQuery(QueryInterface $query)
    {

        $this->checkRequiredParameters($query);

        $this->setEntityNameFromQuery($query);

        $this->validate($query);

        /** @var GetDbValueRepo $repo */
        $repo = $this->getRepo();
        $repo->setEntity($this->entityName);

        $result = $repo->fetchOneEntityByX(
            $query->getFilterProperty(),
            [$query->getFilterValue(), Query::HYDRATE_ARRAY]
        );

        return $result;
    }

    /**
     * @param GetDbValueQuery $query
     * @throws \Exception
     */
    private function checkRequiredParameters(QueryInterface $query): void
    {
        $missing = [];

        foreach ($this->requiredParameters as $parameter) {
            $getter = $this->cliParamNameToGetter($parameter);
            $value = $query->$getter();

            if (!$value || $value == '') {
                $missing[] = $parameter;
            }
        }

        if (count($missing)) {
            throw new \Exception('The following required parameters are empty: ' . implode(', ', $missing));
        }
    }

    private function cliParamNameToGetter(string $parameter): string
    {
        $noDash = str_replace('-', ' ', $parameter);
        $upperCase = ucwords($noDash);
        $getter = 'get' . str_replace(' ', '', $upperCase);
        return $getter;
    }

    private function isValidEntity(): bool
    {
        return class_exists($this->entityName);
    }

    private function isValidProperty(string $property): bool
    {
        return property_exists($this->entityName, $property);
    }

    /**
     * @param GetDbValueQuery $query
     * @throws \Exception
     */
    private function validate(GetDbValueQuery $query): void
    {
        if (!$this->isValidEntity()) {
            throw new \Exception(
                '"' . $query->getEntityName() . '" is not a valid entity'
            );
        }

        if (!$this->isValidProperty($query->getPropertyName())) {
            throw new \Exception(
                '"' . $query->getPropertyName() . '" is not a valid property of "' . $query->getEntityName() . '"'
            );
        }

        if (!$this->isValidProperty($query->getFilterProperty())) {
            throw new \Exception(
                '"' . $query->getFilterProperty() . '" is not a valid property of "' . $query->getEntityName() . '"'
            );
        }
    }
}



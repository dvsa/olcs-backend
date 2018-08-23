<?php

namespace Dvsa\Olcs\Cli\Domain\Query\Util;

use Dvsa\Olcs\Transfer\Query\AbstractQuery;
use Dvsa\Olcs\Transfer\Util\Annotation as Transfer;

/**
 * Class GetDbValue
 *
 * @package Dvsa\Olcs\Cli\Domain\Query\Util
 */
class GetDbValue extends AbstractQuery
{
    /**
     * @var string
     */
    protected $propertyName;

    /**
     * @var string
     */
    protected $entityName;

    /**
     * @var string
     */
    protected $filterProperty;

    /**
     * @var mixed
     */
    protected $filterValue;

    /**
     * @return string
     */
    public function getPropertyName(): ?string
    {
        return $this->propertyName;
    }

    /**
     * @param string $propertyName
     */
    public function setPropertyName(string $propertyName): void
    {
        $this->propertyName = $propertyName;
    }

    /**
     * @return string
     */
    public function getEntityName(): ?string
    {
        return $this->entityName;
    }

    /**
     * @param string $entityName
     */
    public function setEntityName(string $entityName): void
    {
        $this->entityName = $entityName;
    }

    /**
     * @return string
     */
    public function getFilterProperty(): ?string
    {
        return $this->filterProperty;
    }

    /**
     * @param string $filterProperty
     */
    public function setFilterProperty(string $filterProperty): void
    {
        $this->filterProperty = $filterProperty;
    }

    /**
     * @return mixed
     */
    public function getFilterValue()
    {
        return $this->filterValue;
    }

    /**
     * @param mixed $filterValue
     */
    public function setFilterValue($filterValue): void
    {
        $this->filterValue = $filterValue;
    }
}

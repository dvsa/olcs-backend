<?php

namespace Dvsa\Olcs\Cli\Domain\Query\Util;

use Dvsa\Olcs\Transfer\Query\AbstractQuery;
use Dvsa\Olcs\Transfer\Util\Annotation as Transfer;

class getDbValue extends AbstractQuery
{
    /**
     * @var string
     */
    protected $columnName;

    /**
     * @var string
     */
    protected $tableName;

    /**
     * @var string
     */
    protected $filterName;

    /**
     * @var mixed
     */
    protected $filterValue;

    /**
     * @return string
     */
    public function getColumnName(): string
    {
        return $this->columnName;
    }

    /**
     * @return string
     */
    public function getTableName(): string
    {
        return $this->tableName;
    }

    /**
     * @return string
     */
    public function getFilterName(): string
    {
        return $this->filterName;
    }

    /**
     * @return mixed
     */
    public function getFilterValue()
    {
        return $this->filterValue;
    }

}
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
    public function handleQuery(QueryInterface $query)
    {

        protected $repoServiceName = $query->getTableName();

    }

}
<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * Stub class of AbstractQueryHandler handler for testing
 */
class AbstractQueryHandlerStub extends AbstractQueryHandler
{
    public function auditRead($entity)
    {
        return parent::auditRead($entity);
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function handleQuery(QueryInterface $query)
    {
    }
}

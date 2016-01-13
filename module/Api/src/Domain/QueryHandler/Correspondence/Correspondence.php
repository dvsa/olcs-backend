<?php

/**
 * Correspondence
 *
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\Correspondence;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * Correspondence
 *
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 */
class Correspondence extends AbstractQueryHandler
{
    protected $repoServiceName = 'Correspondence';

    public function handleQuery(QueryInterface $query)
    {
        return $this->getRepo()->fetchUsingId($query);
    }
}

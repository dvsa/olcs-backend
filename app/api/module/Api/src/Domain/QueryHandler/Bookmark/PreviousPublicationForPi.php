<?php

/**
 * PreviousPublicationForPi query handler
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\Bookmark;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * PreviousPublicationForPi query handler
 */
class PreviousPublicationForPi extends AbstractQueryHandler
{
    protected $repoServiceName = 'PublicationLink';

    public function handleQuery(QueryInterface $query)
    {
        return $this->getRepo()->fetchPreviousPublicationNo($query);
    }
}

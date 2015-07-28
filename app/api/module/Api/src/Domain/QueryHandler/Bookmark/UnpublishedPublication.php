<?php

/**
 * Unpublished Publication
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\Bookmark;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * Unpublished Publication
 */
class UnpublishedPublication extends AbstractQueryHandler
{
    protected $repoServiceName = 'PublicationLink';

    public function handleQuery(QueryInterface $query)
    {
        return $this->getRepo()->fetchSingleUnpublished($query);
    }
}

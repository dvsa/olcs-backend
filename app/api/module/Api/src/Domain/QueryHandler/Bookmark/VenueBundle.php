<?php

/**
 * Venue Bundle Bookmark
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\Bookmark;

use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * Venue Bundle Bookmark
 */
class VenueBundle extends AbstractBundle
{
    protected $repoServiceName = 'Venue';

    public function handleQuery(QueryInterface $query)
    {
        return $this->getRepo()->fetchUsingId($query);
    }
}

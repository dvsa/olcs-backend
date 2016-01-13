<?php

/**
 * Pi Venue Bundle Bookmark
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\Bookmark;

use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * Pi Venue Bundle Bookmark
 */
class PiVenueBundle extends AbstractBundle
{
    protected $repoServiceName = 'PiVenue';

    public function handleQuery(QueryInterface $query)
    {
        return $this->getRepo()->fetchUsingId($query);
    }
}

<?php

/**
 * CommunityLicBundle Bookmark
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\Bookmark;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * CommunityLicBundle Bookmark
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class CommunityLicBundle extends AbstractQueryHandler
{
    protected $repoServiceName = 'CommunityLic';

    public function handleQuery(QueryInterface $query)
    {
        $comunityLic = $this->getRepo()->fetchUsingId($query);

        return $this->result(
            $comunityLic,
            $query->getBundle()
        )->serialize();
    }
}

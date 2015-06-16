<?php

/**
 * PiHearingBundle Bookmark
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\Bookmark;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * PiHearingBundle Bookmark
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class PiHearingBundle extends AbstractQueryHandler
{
    protected $repoServiceName = 'PiHearing';

    public function handleQuery(QueryInterface $query)
    {
        $piHearing = $this->getRepo()->fetchUsingId($query);

        return $this->result(
            $piHearing,
            $query->getBundle()
        )->serialize();
    }
}

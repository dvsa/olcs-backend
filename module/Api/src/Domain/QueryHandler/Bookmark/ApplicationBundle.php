<?php

/**
 * ApplicationBundle Bookmark
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\Bookmark;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * ApplicationBundle Bookmark
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ApplicationBundle extends AbstractQueryHandler
{
    protected $repoServiceName = 'Application';

    public function handleQuery(QueryInterface $query)
    {
        $application = $this->getRepo()->fetchUsingId($query);

        return $this->result(
            $application,
            $query->getBundle()
        )->serialize();
    }
}

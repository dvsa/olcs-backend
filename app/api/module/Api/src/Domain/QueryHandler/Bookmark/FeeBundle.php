<?php

/**
 * FeeBundle Bookmark
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\Bookmark;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * FeeBundle Bookmark
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class FeeBundle extends AbstractQueryHandler
{
    protected $repoServiceName = 'Fee';

    public function handleQuery(QueryInterface $query)
    {
        $fee = $this->getRepo()->fetchUsingId($query);

        return $this->result(
            $fee,
            $query->getBundle()
        )->serialize();
    }
}

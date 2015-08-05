<?php

/**
 * Previous hearing query handler
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\Bookmark;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * Previous hearing query handler
 */
class PreviousHearing extends AbstractQueryHandler
{
    protected $repoServiceName = 'PiHearing';

    public function handleQuery(QueryInterface $query)
    {
        return $this->getRepo()->fetchPreviousHearing($query->getPi(), new \DateTime($query->getHearingDate()));
    }
}

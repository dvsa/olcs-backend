<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Submission;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * Submission
 */
final class Submission extends AbstractQueryHandler
{
    protected $repoServiceName = 'Submission';

    public function handleQuery(QueryInterface $query)
    {
        return $this->result(
            $this->getRepo()->fetchUsingId($query),
            [
                'case'
            ]
        );
    }
}

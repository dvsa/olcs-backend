<?php

/**
 * SubmissionAction
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\Submission;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * SubmissionAction
 */
class SubmissionAction extends AbstractQueryHandler
{
    protected $repoServiceName = 'SubmissionAction';

    public function handleQuery(QueryInterface $query)
    {
        return $this->result(
            $this->getRepo()->fetchUsingId($query),
            ['actionTypes', 'reasons']
        );
    }
}

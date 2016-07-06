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
        $repo = $this->getRepo();

        // retrieve reason even if deleted
        $repo->disableSoftDeleteable(
            [
                \Dvsa\Olcs\Api\Entity\Pi\Reason::class
            ]
        );

        return $this->result(
            $repo->fetchUsingId($query),
            ['actionTypes', 'reasons']
        );
    }
}

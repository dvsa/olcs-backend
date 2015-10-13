<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Ebsr;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Dvsa\Olcs\Api\Domain\Repository\EbsrSubmission as EbsrSubmissionRepo;

/**
 * EbsrSubmissionList
 */
final class SubmissionList extends AbstractQueryHandler
{
    protected $repoServiceName = 'EbsrSubmission';

    public function handleQuery(QueryInterface $query)
    {
        /** @var EbsrSubmissionRepo $repo */
        $repo = $this->getRepo();
        return [
            'result' => $this->resultList(
                $repo->fetchList($query, Query::HYDRATE_OBJECT),
                [
                    'busReg' => [
                        'otherServices',
                        'licence' => [
                            'organisation'
                        ]
                    ]
                ]
            ),
            'count' => $repo->fetchCount($query)
        ];
    }
}

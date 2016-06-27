<?php

/**
 * EbsrSubmission List
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\Bus\Ebsr;

use Doctrine\Common\Collections\Criteria;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Dvsa\Olcs\Api\Domain\Repository\EbsrSubmission as Repository;
use Doctrine\ORM\Query as DoctrineQuery;
use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareTrait;
use Dvsa\Olcs\Api\Domain\Query\Bus\EbsrSubmissionList as ListDto;
use Doctrine\ORM\Query;

/**
 * EbsrSubmissionList
 * This QueryHandler will query one of two tables.
 * Either the EbsrSubmission table (for LAs) or the EbsrSubmission table (operators/organisation users).
 */
class EbsrSubmissionList extends AbstractQueryHandler implements AuthAwareInterface
{
    use AuthAwareTrait;

    protected $repoServiceName = 'EbsrSubmission';

    /**
     * @return array
     */
    public function handleQuery(QueryInterface $query)
    {
        /** @var Repository $repo */
        $repo = $this->getRepo();

        // get data from transfer query
        $data = $query->getArrayCopy();

        $data['organisation'] = $this->getCurrentOrganisation()->getId();

        $listDto = ListDto::create($data);
        $results = $repo->fetchList($listDto, Query::HYDRATE_OBJECT);

        return [
            'results' => $this->resultList(
                $results,
                [
                    'busReg' => [
                        'licence' => [
                            'organisation'
                        ],
                        'otherServices',
                    ],
                    'document'
                ]
            ),
            'count' => $repo->fetchCount($listDto)
        ];
    }
}

<?php

/**
 * TxcInbox List
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\Bus\Ebsr;

use Doctrine\Common\Collections\Criteria;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Entity\Bus\LocalAuthority;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Dvsa\Olcs\Api\Domain\Repository\TxcInbox as Repository;
use Doctrine\ORM\Query as DoctrineQuery;
use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareTrait;
use Dvsa\Olcs\Api\Domain\Query\Bus\TxcInboxList as ListDto;
use Doctrine\ORM\Query;

/**
 * TxcInboxList
 * This QueryHandler will query one of two tables.
 * Either the TxcInbox table (for LAs) or the EbsrSubmission table (operators/organisation users).
 */
class TxcInboxList extends AbstractQueryHandler implements AuthAwareInterface
{
    use AuthAwareTrait;

    protected $repoServiceName = 'TxcInbox';
    protected $extraRepos = ['EbsrSubmission'];

    /**
     * @return array
     */
    public function handleQuery(QueryInterface $query)
    {
        /** @var Repository $repo */
        $repo = $this->getRepo();

        // get data from transfer query
        $data = $query->getArrayCopy();

        $data['localAuthority'] = $this->getCurrentUser()->getLocalAuthority()->getId();

        $listDto = ListDto::create($data);

        $results = $repo->fetchList($listDto, Query::HYDRATE_OBJECT);

        return [
            'result' => $this->resultList(
                $results,
                [
                    'busReg' => [
                        'ebsrSubmissions' => [
                            'ebsrSubmissionType',
                            'ebsrSubmissionStatus'
                        ],
                        'licence' => [
                            'organisation'
                        ],
                        'otherServices'
                    ]
                ]
            ),
            'count' => $repo->fetchCount($listDto)
        ];
    }
}

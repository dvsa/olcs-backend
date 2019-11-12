<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Publication;

use Doctrine\ORM\Query as DoctrineQuery;
use Dvsa\Olcs\Api\Domain\AuthAwareTrait;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Dvsa\Olcs\Api\Domain\Repository\Publication as PublicationRepo;
use ZfcRbac\Service\AuthorizationServiceAwareTrait;

/**
 * List of pending publications
 */
final class PendingList extends AbstractQueryHandler
{
    protected $repoServiceName = 'Publication';

    use AuthAwareTrait;

    public function handleQuery(QueryInterface $query)
    {
        $userOsType = $this->getCurrentUser()->getOsType();
        /**
         * @var PublicationRepo $repo
         */
        $repo = $this->getRepo();
        $result = $repo->fetchPendingList($query);

        return [
            'result' => $this->resultList(
                $result['results'],
                [
                    'pubStatus',
                    'trafficArea',
                    'document'
                ]
            ),
            'userOsType' => $userOsType,
            'count' => $result['count']
        ];
    }
}

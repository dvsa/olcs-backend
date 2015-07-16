<?php

/**
 * Transport Manager
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\Tm;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Dvsa\Olcs\Api\Domain\Repository\TransportManager as TransportManagerRepo;

/**
 * Transport Manager
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class TransportManager extends AbstractQueryHandler
{
    protected $repoServiceName = 'TransportManager';

    public function handleQuery(QueryInterface $query)
    {
        /** @var InspectionRequestRepo $repo */
        $repo = $this->getRepo();
        $transportManager = $repo->fetchUsingId($query);

        return $this->result(
            $transportManager,
            [
                'tmType',
                'tmStatus',
                'homeCd' => [
                    'person' => [
                        'title'
                    ],
                    'address' => [
                        'countryCode'
                    ]
                ],
                'workCd' => [
                    'address' => [
                        'countryCode'
                    ]
                ]
            ]
        );
    }
}

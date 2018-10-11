<?php

namespace Dvsa\Olcs\Cli\Domain\QueryHandler\Permits;

use DateTime;
use Dvsa\Olcs\Api\Domain\Exception\RuntimeException;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Domain\Repository\IrhpCandidatePermit;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Dvsa\Olcs\Transfer\Query\IrhpCandidatePermit\GetScoredList as Query;

/**
 * Get a list of scored irhp candidate permit records
 * and associated data
 */
class GetScoredList extends AbstractQueryHandler
{
    protected $repoServiceName = 'IrhpCandidatePermit';
    protected $bundledRepos = [
        'irhpPermitApplication' => [
            'ecmtPermitApplication' => [
                'countrys',
                'sectors',
                'internationalJourneys'
            ],
            'irhpPermitWindow',
            'licence' => [
                'trafficArea',
                'organisation'
            ]
        ],
        'irhpPermitRange' => [
            'countrys'
        ],
    ];

    /**
     * Return a list of scored irhp candidate permit records
     * and associated data
     * @param QueryInterface|Query $query DTO
     *
     * @return array
     * @throws RuntimeException
     */
    public function handleQuery(QueryInterface $query)
    {
        /** @var Query $query */
        /** @var IrhpCandidatePermit $repo */
        $repo = $this->getRepo();

        $results = $repo->fetchAllScoredForStock(
            $query->getStockId()
        );

        return [
            'result' => $this->resultList(
                $results,
                $this->bundledRepos
            ),
        ];
    }
}

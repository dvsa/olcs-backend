<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Permits;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * Get all unpaid permits by application and status
 *
 * @author Tonci Vidovic <tonci.vidovic@capgemini.com>
 */
class UnpaidEcmtPermits extends AbstractQueryHandler implements ToggleRequiredInterface
{
    use ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::BACKEND_PERMITS];
    protected $repoServiceName = 'IrhpCandidatePermit';
    protected $bundle = [
        'irhpPermitRange' => [
            'countrys' => [
                'country'
            ],
            'emissionsCategory',
        ],
    ];

    /**
     * Handle list query
     *
     * @param QueryInterface $query query
     *
     * @return array
     */
    public function handleQuery(QueryInterface $query)
    {
        $repo = $this->getRepo();

        // fetch list of candidate permits
        $irhpCandidatePermits = $this->resultList(
            $repo->fetchList($query, Query::HYDRATE_OBJECT),
            $this->bundle
        );

        foreach ($irhpCandidatePermits as $i => $irhpCandidatePermit) {
            // set value of permit number
            $irhpCandidatePermit['permitNumber'] = ($query->getPage() - 1) * $query->getLimit() + $i + 1;

            // TODO - OLCS-25959 set value of constrained countries

            $irhpCandidatePermits[$i] = $irhpCandidatePermit;
        }

        return [
            'result' => $irhpCandidatePermits,
            'count' => $repo->fetchCount($query)
        ];
    }
}

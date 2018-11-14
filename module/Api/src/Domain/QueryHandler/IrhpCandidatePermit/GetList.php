<?php
/**
 * Retrieve Irhp Candidate Permit list
 *
 * @author Tonci Vidovic <tonci.vidovic@capgemini.com>
 */

namespace Dvsa\Olcs\Api\Domain\QueryHandler\IrhpCandidatePermit;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\Permits\EcmtConstrainedCountriesList;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Doctrine\ORM\Query;

class GetList extends AbstractQueryHandler implements ToggleRequiredInterface
{
    use ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::ADMIN_PERMITS];
    protected $repoServiceName = 'IrhpCandidatePermit';
    protected $bundle = [
        'irhpPermitRange' => [
            'countrys' => [
                'country'
            ]
        ],
    ];

    /**
     * handle list query
     *
     * @param QueryInterface $query query
     *
     * @return array
     */
    public function handleQuery(QueryInterface $query)
    {
        $restrictedCountries = $this->getQueryHandler()->handleQuery(
            EcmtConstrainedCountriesList::create(['hasEcmtConstraints' => 1])
        );

        $repo = $this->getRepo();
        $irhpCandidatePermits = $this->resultList(
            $repo->fetchList($query, Query::HYDRATE_OBJECT),
            $this->bundle
        );

        $allCountries = $restrictedCountries['result'];
        $allCountryIds = array_column($allCountries, 'id');

        foreach ($irhpCandidatePermits as $permitKey => $permit) {
            $includedCountryIds = array_column($permit['irhpPermitRange']['countrys'], 'id');
            $excludedCountryIds = array_diff($allCountryIds, $includedCountryIds);

            $constrainedCountries = [];
            foreach ($allCountries as $country) {
                if (in_array($country['id'], $excludedCountryIds)) {
                    $constrainedCountries[] = $country;
                }
            }

            $irhpCandidatePermits[$permitKey]['constrainedCountries'] = $constrainedCountries;
        }

        return [
            'result' => $irhpCandidatePermits,
            'count' => $repo->fetchCount($query)
        ];
    }
}

<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\IrhpCandidatePermit;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Api\Service\Permits\Common\RangeBasedRestrictedCountriesProvider;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Get IRHP Candidate Permits by IRHP Application id
 */
class GetListByIrhpApplication extends AbstractQueryHandler implements ToggleRequiredInterface
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

    /** @var RangeBasedRestrictedCountriesProvider */
    private $restrictedCountriesProvider;

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator Service Manager
     *
     * @return $this
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $mainServiceLocator = $serviceLocator->getServiceLocator();

        $this->restrictedCountriesProvider
            = $mainServiceLocator->get('PermitsCommonRangeBasedRestrictedCountriesProvider');

        return parent::createService($serviceLocator);
    }

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

            // set value of constrained countries
            $irhpCandidatePermit['constrainedCountries']
                = $this->restrictedCountriesProvider->getList($irhpCandidatePermit['irhpPermitRange']['id']);

            $irhpCandidatePermits[$i] = $irhpCandidatePermit;
        }

        return [
            'results' => $irhpCandidatePermits,
            'count' => $repo->fetchCount($query)
        ];
    }
}
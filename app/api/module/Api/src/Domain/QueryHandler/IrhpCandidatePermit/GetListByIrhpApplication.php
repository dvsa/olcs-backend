<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\IrhpCandidatePermit;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Service\Permits\Common\RangeBasedRestrictedCountriesProvider;
use Dvsa\Olcs\Transfer\Query\PagedQueryInterface;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * Get IRHP Candidate Permits by IRHP Application id
 */
class GetListByIrhpApplication extends AbstractQueryHandler
{
    protected $repoServiceName = 'IrhpCandidatePermit';
    protected $bundle = [
        'irhpPermitRange' => [
            'countrys' => [
                'country'
            ],
            'emissionsCategory',
        ],
        'irhpPermitApplication'
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
            $permitNumber = $i + 1;
            if ($query instanceof PagedQueryInterface) {
                $permitNumber += (($query->getPage() - 1) * $query->getLimit());
            }
            $irhpCandidatePermit['permitNumber'] = $permitNumber;

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

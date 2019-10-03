<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\IrhpCandidatePermit;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitType;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
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
    protected $extraRepos = ['IrhpApplication', 'Country'];
    protected $bundle = [
        'irhpPermitRange' => [
            'countrys' => [
                'country'
            ],
            'emissionsCategory',
        ],
    ];

    /** @var array */
    private $config = [];

    /** @var array */
    private $constrainedCountries = [];

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

        $this->config = $mainServiceLocator->get('config');

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
        // fetch list of candidate permits
        $repo = $this->getRepo();

        $irhpCandidatePermits = $this->resultList(
            $repo->fetchList($query, Query::HYDRATE_OBJECT),
            $this->bundle
        );

        if (!empty($irhpCandidatePermits)) {
            // fetch the application
            /** @var IrhpApplication $irhpApplication */
            $irhpApplication = $this->getRepo('IrhpApplication')->fetchById($query->getIrhpApplication());

            // get list of restricted countries
            $restrictedCountries = $this->getRestrictedCountries($irhpApplication->getIrhpPermitType());

            foreach ($irhpCandidatePermits as $i => $irhpCandidatePermit) {
                // set value of permit number
                $irhpCandidatePermit['permitNumber'] = ($query->getPage() - 1) * $query->getLimit() + $i + 1;

                // set value of constrained countries
                $irhpCandidatePermit['constrainedCountries']
                    = $this->getConstrainedCountries($irhpCandidatePermit, $restrictedCountries);

                $irhpCandidatePermits[$i] = $irhpCandidatePermit;
            }
        }

        return [
            'results' => $irhpCandidatePermits,
            'count' => $repo->fetchCount($query)
        ];
    }

    /**
     * Get restricted countries
     *
     * @param IrhpPermitType $irhpPermitType
     *
     * @return array
     */
    protected function getRestrictedCountries(IrhpPermitType $irhpPermitType)
    {
        $irhpPermitTypeId = $irhpPermitType->getId();

        if (empty($this->config['permits']['types'][$irhpPermitTypeId]['restricted_countries'])) {
            return [];
        }

        return $this->getRepo('Country')->fetchByIds(
            $this->config['permits']['types'][$irhpPermitTypeId]['restricted_countries'],
            Query::HYDRATE_ARRAY
        );
    }

    /**
     * Get constrained countries
     *
     * @param array $irhpCandidatePermit
     * @param array $restrictedCountries
     *
     * @return array
     */
    protected function getConstrainedCountries(array $irhpCandidatePermit, array $restrictedCountries)
    {
        if (empty($irhpCandidatePermit['irhpPermitRange'])) {
            return $restrictedCountries;
        }

        $irhpPermitRange = $irhpCandidatePermit['irhpPermitRange'];
        $irhpPermitRangeId = $irhpPermitRange['id'];

        if (!isset($this->constrainedCountries[$irhpPermitRangeId])) {
            // ids of countries included in the range
            $includedCountryIds = array_column($irhpPermitRange['countrys'], 'id');

            $constrainedCountries = [];

            foreach ($restrictedCountries as $country) {
                if (!in_array($country['id'], $includedCountryIds)) {
                    // the restricted country is not specifically included in the range
                    // therefore it is a constrained country
                    $constrainedCountries[] = $country;
                }
            }

            $this->constrainedCountries[$irhpPermitRangeId] = $constrainedCountries;
        }

        return $this->constrainedCountries[$irhpPermitRangeId];
    }
}

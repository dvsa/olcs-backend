<?php
/**
 * Retrieve Irhp Permit list by Irhp ID
 *
 * @author Andy Newton <andy@vitri.ltd>
 */

namespace Dvsa\Olcs\Api\Domain\QueryHandler\IrhpPermit;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication;
use Dvsa\Olcs\Api\Service\Permits\Common\RangeBasedRestrictedCountriesProvider;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class GetListByIrhpId extends AbstractQueryHandler
{
    protected $repoServiceName = 'IrhpPermit';
    protected $extraRepos = ['IrhpApplication'];
    protected $bundle = [
        'replaces',
        'irhpPermitRange' => [
            'journey',
            'irhpPermitStock' => [
                'country',
                'irhpPermitType',
                'permitCategory',
            ],
            'emissionsCategory',
        ],
        'irhpPermitApplication',
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
    public function createService(ServiceLocatorInterface $serviceLocator, $name = null, $requestedName = null)
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

        // fetch list of permits
        $irhpPermits = $this->resultList(
            $repo->fetchList($query, Query::HYDRATE_OBJECT),
            $this->bundle
        );

        // fetch the application
        /** @var IrhpApplication $irhpApplication */
        $irhpApplication = $this->getRepo('IrhpApplication')->fetchById($query->getIrhpApplication());

        if ($irhpApplication->getIrhpPermitType()->isConstrainedCountriesType()) {
            // calculate constrainedCountries as applicable
            foreach ($irhpPermits as $i => $irhpPermit) {
                // set value of constrained countries
                $irhpPermit['constrainedCountries']
                    = $this->restrictedCountriesProvider->getList($irhpPermit['irhpPermitRange']['id']);

                $irhpPermits[$i] = $irhpPermit;
            }
        }

        return [
            'results' => $irhpPermits,
            'count' => $repo->fetchCount($query)
        ];
    }
}

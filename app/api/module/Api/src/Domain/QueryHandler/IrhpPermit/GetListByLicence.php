<?php
/**
 * Retrieve Irhp Permit list
 *
 * @author Tonci Vidovic <tonci.vidovic@capgemini.com>
 */

namespace Dvsa\Olcs\Api\Domain\QueryHandler\IrhpPermit;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitType;
use Dvsa\Olcs\Api\Service\Permits\Common\RangeBasedRestrictedCountriesProvider;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

class GetListByLicence extends AbstractQueryHandler
{
    protected $repoServiceName = 'IrhpPermit';

    protected $bundle = [
        'irhpPermitApplication',
        'irhpPermitRange' => [
            'journey',
            'irhpPermitStock' => [
                'irhpPermitType' => ['name'],
                'country',
                'permitCategory',
            ],
            'emissionsCategory',
        ]
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

        // fetch list of permits
        $irhpPermits = $this->resultList(
            $repo->fetchList($query, Query::HYDRATE_OBJECT),
            $this->bundle
        );

        foreach ($irhpPermits as $i => $irhpPermit) {
            $currentPermitTypeId = $irhpPermit['irhpPermitRange']['irhpPermitStock']['irhpPermitType']['id'];

            if (in_array($currentPermitTypeId, IrhpPermitType::CONSTRAINED_COUNTRIES_TYPES)) {
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

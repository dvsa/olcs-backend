<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\IrhpPermit;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication;
use Dvsa\Olcs\Api\Service\Permits\Common\RangeBasedRestrictedCountriesProvider;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Retrieve Irhp Permit list by Irhp ID
 *
 * @author Andy Newton <andy@vitri.ltd>
 */
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
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function createService(ServiceLocatorInterface $serviceLocator, $name = null, $requestedName = null)
    {
        return $this->__invoke($serviceLocator, GetListByIrhpId::class);
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

    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return GetListByIrhpId
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        if (method_exists($container, 'getServiceLocator') && $container->getServiceLocator()) {
            $fullContainer = $container;
            $container = $container->getServiceLocator();
        }

        $this->restrictedCountriesProvider
            = $container->get('PermitsCommonRangeBasedRestrictedCountriesProvider');
        return parent::__invoke($fullContainer, $requestedName, $options);
    }
}

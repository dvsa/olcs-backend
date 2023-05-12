<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Permits;

use DateTime;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitType;
use Dvsa\Olcs\Api\Service\Permits\Availability\StockAvailabilityChecker;
use Dvsa\Olcs\Transfer\Query\Permits\AvailableStocks as AvailableStocksQuery;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Available stocks
 */
class AvailableStocks extends AbstractQueryHandler
{
    protected $repoServiceName = 'IrhpPermitWindow';

    /** @var StockAvailabilityChecker */
    private $stockAvailabilityChecker;

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
        return $this->__invoke($serviceLocator, AvailableStocks::class);
    }

    /**
     * Handle query
     *
     * @param QueryInterface|AvailableStocksQuery $query query
     *
     * @return array
     */
    public function handleQuery(QueryInterface $query)
    {
        $irhpPermitType = $query->getIrhpPermitType();

        $supportedTypes = [
            IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT,
            IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT_SHORT_TERM,
        ];

        if (!in_array($irhpPermitType, $supportedTypes)) {
            return [
                'stocks' => [],
                'hasStocks' => false,
            ];
        }

        $year = $query->getYear();

        if ($query->getYear()) {
            $openWindows = $this->getRepo()->fetchOpenWindowsByTypeYear(
                $irhpPermitType,
                new DateTime(),
                $year,
                $this->isInternalUser()
            );
        } else {
            $openWindows = $this->getRepo()->fetchOpenWindowsByType(
                $irhpPermitType,
                new DateTime(),
                $this->isInternalUser()
            );
        }

        $availableStocks = [];

        foreach ($openWindows as $window) {
            $irhpPermitStock = $window->getIrhpPermitStock();
            $irhpPermitStockId = $irhpPermitStock->getId();

            $includeStock = !isset($availableStocks[$irhpPermitStockId])
                && ($irhpPermitType != IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT_SHORT_TERM
                    || $this->stockAvailabilityChecker->hasAvailability($irhpPermitStockId)
                );

            if ($includeStock) {
                $availableStocks[$irhpPermitStockId] = [
                    'id' => $irhpPermitStockId,
                    'periodNameKey' => $irhpPermitStock->getPeriodNameKey(),
                    'year' => $irhpPermitStock->getValidityYear(),
                ];
            }
        }

        // make sure it's ordered by stock id
        ksort($availableStocks);

        return [
            'stocks' => $availableStocks,
            'hasStocks' => !empty($availableStocks),
        ];
    }

    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return AvailableStocks
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $fullContainer = $container;

        if (method_exists($container, 'getServiceLocator') && $container->getServiceLocator()) {
            $container = $container->getServiceLocator();
        }

        $this->stockAvailabilityChecker = $container->get('PermitsAvailabilityStockAvailabilityChecker');
        return parent::__invoke($fullContainer, $requestedName, $options);
    }
}

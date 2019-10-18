<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Permits;

use DateTime;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitType;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Api\Service\Permits\ShortTermEcmt\StockAvailabilityChecker;
use Dvsa\Olcs\Transfer\Query\Permits\AvailableStocks as AvailableStocksQuery;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Available stocks
 */
class AvailableStocks extends AbstractQueryHandler implements ToggleRequiredInterface
{
    use ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::BACKEND_PERMITS];

    protected $repoServiceName = 'IrhpPermitWindow';

    /** @var StockAvailabilityChecker */
    private $stockAvailabilityChecker;

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

        $this->stockAvailabilityChecker = $mainServiceLocator->get('PermitsShortTermEcmtStockAvailabilityChecker');

        return parent::createService($serviceLocator);
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

        $openWindows = $this->getRepo()->fetchOpenWindowsByTypeYear(
            $irhpPermitType,
            new DateTime(),
            $year
        );

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
}

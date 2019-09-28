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
        $year = $query->getYear();

        // only ECMT Short-term for 2020 should use it
        if ($irhpPermitType != IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT_SHORT_TERM || $year != 2020) {
            return [
                'stocks' => []
            ];
        }

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
                && $this->stockAvailabilityChecker->hasAvailability($irhpPermitStockId);

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
        ];
    }
}

<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Permits;

use DateTime;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitType;
use Dvsa\Olcs\Api\Service\Permits\Availability\StockAvailabilityChecker;
use Dvsa\Olcs\Transfer\Query\Permits\AvailableYears as AvailableYearsQuery;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * Available years
 */
class AvailableYears extends AbstractQueryHandler
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
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $mainServiceLocator = $serviceLocator->getServiceLocator();

        $this->stockAvailabilityChecker = $mainServiceLocator->get('PermitsAvailabilityStockAvailabilityChecker');

        return parent::createService($serviceLocator);
    }

    /**
     * Handle query
     *
     * @param QueryInterface|AvailableYearsQuery $query query
     *
     * @return array
     */
    public function handleQuery(QueryInterface $query)
    {
        $permitType = $query->getType();

        $applicableTypes = [
            IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT_SHORT_TERM,
            IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT,
        ];

        if (!in_array($permitType, $applicableTypes)) {
            return [
                'hasYears' => false,
                'years' => []
            ];
        }

        $openWindows = $this->getRepo()->fetchOpenWindowsByType(
            $permitType,
            new DateTime(),
            $this->isInternalUser()
        );

        $availableYears = [];
        foreach ($openWindows as $window) {
            $irhpPermitStock = $window->getIrhpPermitStock();

            $includeYear = true;
            if ($permitType == IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT_SHORT_TERM) {
                $includeYear = $this->stockAvailabilityChecker->hasAvailability($irhpPermitStock->getId());
            }
            
            if ($includeYear) {
                $availableYears[] = $irhpPermitStock->getValidityYear();
            }
        }

        $availableYears = array_unique($availableYears);
        sort($availableYears);

        return [
            'hasYears' => (count($availableYears) > 0),
            'years' => $availableYears,
        ];
    }
}

<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\IrhpApplication;

use DateTime;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitType;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Api\Service\Permits\ShortTermEcmt\StockAvailabilityChecker;
use Dvsa\Olcs\Transfer\Query\IrhpApplication\PermitsAvailableByYear as PermitsAvailableByYearQuery;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use RuntimeException;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * PermitsAvailableByYear
 */
class PermitsAvailableByYear extends AbstractQueryHandler implements ToggleRequiredInterface
{
    /** @var StockAvailabilityChecker */
    private $stockAvailabilityChecker;

    use ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::BACKEND_PERMITS];

    protected $repoServiceName = 'IrhpPermitWindow';

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
     * @param QueryInterface|PermitsAvailableByYearQuery $query query
     *
     * @return array
     */
    public function handleQuery(QueryInterface $query)
    {
        $year = $query->getYear();

        $irhpPermitWindows = $this->getRepo()->fetchOpenWindowsByTypeYear(
            IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT_SHORT_TERM,
            new DateTime(),
            $year
        );

        if (count($irhpPermitWindows) == 0) {
            return $this->createResponse(false);
        }

        if (count($irhpPermitWindows) > 1) {
            throw new RuntimeException(
                'Unexpectedly found multiple windows open for year ' . $year
            );
        }

        $permitsAvailable = $this->stockAvailabilityChecker->hasAvailability(
            $irhpPermitWindows[0]->getIrhpPermitStock()->getId()
        );

        return $this->createResponse($permitsAvailable);
    }

    /**
     * Generate a response array for this query
     *
     * @param bool $permitsAvailable
     *
     * @return array
     */
    private function createResponse($permitsAvailable)
    {
        return ['permitsAvailable' => $permitsAvailable];
    }
}

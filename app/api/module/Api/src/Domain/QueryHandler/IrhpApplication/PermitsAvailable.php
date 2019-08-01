<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\IrhpApplication;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Api\Service\Permits\ShortTermEcmt\StockAvailabilityChecker;
use Dvsa\Olcs\Transfer\Query\IrhpApplication\PermitsAvailable as PermitsAvailableQuery;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * PermitsAvailable
 */
class PermitsAvailable extends AbstractQueryHandler implements ToggleRequiredInterface
{
    /** @var StockAvailabilityChecker */
    private $stockAvailabilityChecker;

    use ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::BACKEND_PERMITS];

    protected $repoServiceName = 'IrhpApplication';

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
     * @param QueryInterface|PermitsAvailableQuery $query query
     *
     * @return array
     */
    public function handleQuery(QueryInterface $query)
    {
        $irhpApplication = $this->getRepo()->fetchUsingId($query);
        if (!$irhpApplication->getIrhpPermitType()->isEcmtShortTerm()) {
            return $this->createResponse(true);
        }

        $irhpPermitWindow = $irhpApplication->getFirstIrhpPermitApplication()->getIrhpPermitWindow();
        if (!$irhpPermitWindow->isActive()) {
            return $this->createResponse(false);
        }

        $permitsAvailable = $this->stockAvailabilityChecker->hasAvailability(
            $irhpPermitWindow->getIrhpPermitStock()->getId()
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

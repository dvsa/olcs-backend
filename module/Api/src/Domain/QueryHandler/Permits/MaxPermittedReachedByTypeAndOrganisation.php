<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Permits;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitType;
use Dvsa\Olcs\Api\Service\Common\CurrentDateTimeFactory;
use Dvsa\Olcs\Api\Service\Permits\Availability\StockLicenceMaxPermittedCounter;
use Dvsa\Olcs\Transfer\Query\Permits\MaxPermittedReachedByTypeAndOrganisation
    as MaxPermittedReachedByTypeAndOrganisationQry;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

/**
 * Max permitted reached by type and organisation
 */
class MaxPermittedReachedByTypeAndOrganisation extends AbstractQueryHandler
{
    protected $repoServiceName = 'IrhpPermitWindow';

    protected $extraRepos = ['Organisation'];

    /** @var CurrentDateTimeFactory */
    private $currentDateTimeFactory;

    /** @var StockLicenceMaxPermittedCounter */
    private $stockLicenceMaxPermittedCounter;

    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator Service Manager
     *
     * @return self
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $mainServiceLocator = $serviceLocator->getServiceLocator();

        $this->currentDateTimeFactory = $mainServiceLocator->get('CommonCurrentDateTimeFactory');

        $this->stockLicenceMaxPermittedCounter = $mainServiceLocator->get(
            'PermitsAvailabilityStockLicenceMaxPermittedCounter'
        );

        return parent::createService($serviceLocator);
    }

    /**
     * Handle query
     *
     * @param QueryInterface|MaxPermittedReachedByTypeAndOrganisationQry $query
     *
     * @return array
     */
    public function handleQuery(QueryInterface $query)
    {
        $irhpPermitTypeId = $query->getIrhpPermitType();
        if ($irhpPermitTypeId != IrhpPermitType::IRHP_PERMIT_TYPE_ID_ECMT) {
            return $this->generateResponse(false);
        }

        $openWindows = $this->getRepo()->fetchOpenWindowsByType(
            $irhpPermitTypeId,
            $this->currentDateTimeFactory->create()
        );

        $organisation = $this->getRepo('Organisation')->fetchById(
            $query->getOrganisation()
        );
        $eligibleLicences = $organisation->getEligibleIrhpLicences();

        foreach ($openWindows as $irhpPermitWindow) {
            $irhpPermitStock = $irhpPermitWindow->getIrhpPermitStock();

            foreach ($eligibleLicences as $licence) {
                $maxPermitted = $this->stockLicenceMaxPermittedCounter->getCount($irhpPermitStock, $licence);

                if ($maxPermitted > 0) {
                    return $this->generateResponse(false);
                }
            }
        }

        return $this->generateResponse(true);
    }

    /**
     * Generate a response array for this query
     *
     * @param bool $maxPermittedReached
     *
     * @return array
     */
    private function generateResponse($maxPermittedReached)
    {
        return ['maxPermittedReached' => $maxPermittedReached];
    }
}

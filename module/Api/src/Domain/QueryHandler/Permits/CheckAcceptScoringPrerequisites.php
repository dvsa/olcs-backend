<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Permits;

use Dvsa\Olcs\Api\Domain\Query\Permits\CheckAcceptScoringPrerequisites as CheckAcceptScoringPrerequisitesQuery;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Api\Service\Permits\Scoring\ScoringQueryProxy;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Check accept scoring prerequisites
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class CheckAcceptScoringPrerequisites extends AbstractQueryHandler implements ToggleRequiredInterface
{
    use ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::BACKEND_PERMITS];

    protected $repoServiceName = 'IrhpPermitRange';

    protected $extraRepos = ['IrhpPermit'];

    /** @var ScoringQueryProxy */
    private $scoringQueryProxy;

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

        $this->scoringQueryProxy = $mainServiceLocator->get('PermitsScoringScoringQueryProxy');

        return parent::createService($serviceLocator);
    }

    /**
     * Handle query
     *
     * @param QueryInterface|CheckAcceptScoringPrerequisitesQuery $query query
     *
     * @return array
     */
    public function handleQuery(QueryInterface $query)
    {
        $stockId = $query->getId();

        $emissionsCategories = [
            RefData::EMISSIONS_CATEGORY_EURO6_REF => 'Euro 6',
            RefData::EMISSIONS_CATEGORY_EURO5_REF => 'Euro 5'
        ];

        if (!$this->scoringQueryProxy->hasInScopeUnderConsiderationApplications($stockId)) {
            return $this->generateResponse(false, 'No under consideration applications currently in scope');
        }

        foreach ($emissionsCategories as $emissionsCategoryId => $emissionsCategoryCaption) {
            $permitsRequired = $this->scoringQueryProxy->getSuccessfulCountInScope(
                $stockId,
                $emissionsCategoryId
            );

            if ($permitsRequired > 0) {
                $combinedRangeSize = $this->getRepo()->getCombinedRangeSize(
                    $stockId,
                    $emissionsCategoryId
                );

                if (is_null($combinedRangeSize)) {
                    return $this->generateResponse(
                        false,
                        sprintf(
                            '%d %s permits required but no %s ranges available',
                            $permitsRequired,
                            $emissionsCategoryCaption,
                            $emissionsCategoryCaption
                        )
                    );
                }

                $assignedPermits = $this->getRepo('IrhpPermit')->getPermitCount(
                    $stockId,
                    $emissionsCategoryId
                );

                $permitsAvailable = $combinedRangeSize - $assignedPermits;
                if ($permitsAvailable < $permitsRequired) {
                    return $this->generateResponse(
                        false,
                        sprintf(
                            'Insufficient %s permits available - %s available, %s required',
                            $emissionsCategoryCaption,
                            $permitsAvailable,
                            $permitsRequired
                        )
                    );
                }
            }
        }

        return $this->generateResponse(true, 'Prerequisites passed');
    }

    /**
     * Generate an array representing the query response
     *
     * @param bool $permitted
     * @param string $message
     *
     * @return array
     */
    private function generateResponse($permitted, $message)
    {
        return [
            'result' => $permitted,
            'message' => $message
        ];
    }
}

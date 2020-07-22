<?php
/**
 * IrhpPermitRanges by IrhpApplication
 *
 * @author Andy Newton <andy@vitri.ltd>
 */

namespace Dvsa\Olcs\Api\Domain\QueryHandler\IrhpApplication;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication;
use Dvsa\Olcs\Api\Service\Permits\ShortTermEcmt\CandidatePermitsAvailableCountCalculator;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class RangesByIrhpApplication extends AbstractQueryHandler
{
    protected $repoServiceName = 'IrhpApplication';
    protected $bundle = ['countrys', 'emissionsCategory'];

    /** @var CandidatePermitsAvailableCountCalculator $candidatePermitsAvailableCountCalculator */
    protected $candidatePermitsAvailableCountCalculator;

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

        $this->candidatePermitsAvailableCountCalculator = $mainServiceLocator->get('PermitsShortTermEcmtCandidatePermitsAvailableCountCalculator');

        return parent::createService($serviceLocator);
    }

    /**
     * @param QueryInterface $query query
     * @return array
     */
    public function handleQuery(QueryInterface $query)
    {
        /** @var IrhpApplication $irhpApplication */
        $irhpApplication = $this->getRepo()->fetchById($query->getIrhpApplication());

        $ranges = $irhpApplication
            ->getAssociatedStock()
            ->getNonReservedNonReplacementRangesOrderedByFromNo();

        foreach ($ranges as &$range) {
            $range->remainingPermits =
                $this->candidatePermitsAvailableCountCalculator->getCount($range, 0);
        }

        return [
            'ranges' => $this->resultList($ranges, $this->bundle),
            'count' => count($ranges)
        ];
    }
}

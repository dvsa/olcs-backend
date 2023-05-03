<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\IrhpApplication;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication;
use Dvsa\Olcs\Api\Service\Permits\Availability\CandidatePermitsAvailableCountCalculator;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * IrhpPermitRanges by IrhpApplication
 *
 * @author Andy Newton <andy@vitri.ltd>
 */
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
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function createService(ServiceLocatorInterface $serviceLocator, $name = null, $requestedName = null)
    {
        return $this->__invoke($serviceLocator, RangesByIrhpApplication::class);
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

    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return RangesByIrhpApplication
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        if (method_exists($container, 'getServiceLocator') && $container->getServiceLocator()) {
            $fullContainer = $container;
            $container = $container->getServiceLocator();
        }

        $this->candidatePermitsAvailableCountCalculator = $container->get('PermitsAvailabilityCandidatePermitsAvailableCountCalculator');
        return parent::__invoke($fullContainer, $requestedName, $options);
    }
}

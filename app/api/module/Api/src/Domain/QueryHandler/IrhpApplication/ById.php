<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\IrhpApplication;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication;
use Dvsa\Olcs\Api\Service\Permits\Fees\DaysToPayIssueFeeProvider;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Exception;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Retrieve IRHP application by id
 */
class ById extends AbstractQueryHandler
{
    protected $repoServiceName = 'IrhpApplication';
    protected $bundle = [
        'licence' => ['trafficArea', 'organisation'],
        'irhpPermitType' => ['name'],
        'fees' => ['feeType' => ['feeType'], 'feeStatus'],
        'irhpPermitApplications' => ['irhpPermitWindow' => ['irhpPermitStock' => ['country', 'irhpPermitType']]],
        'sectors',
        'countrys',
    ];

    /** @var DaysToPayIssueFeeProvider */
    private $daysToPayIssueFeeProvider;

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
        return $this->__invoke($serviceLocator, ById::class);
    }

    /**
     * Handle query
     *
     * @param QueryInterface $query query
     *
     * @return array
     */
    public function handleQuery(QueryInterface $query)
    {
        /** @var IrhpApplication $irhpApplication */
        $irhpApplication = $this->getRepo()->fetchUsingId($query);

        $daysToPayIssueFee = $this->daysToPayIssueFeeProvider->getDays();
        foreach ($irhpApplication->getFees() as $fee) {
            $fee->setDaysToPayIssueFee($daysToPayIssueFee);
        }

        $this->auditRead($irhpApplication);

        try {
            $totalPermitsRequired = $irhpApplication->calculateTotalPermitsRequired();
            $totalPermitsAwarded = $irhpApplication->getPermitsAwarded();
        } catch (Exception $e) {
            $totalPermitsRequired = null;
            $totalPermitsAwarded = null;
        }

        return $this->result(
            $irhpApplication,
            $this->bundle,
            [
                'canViewCandidatePermits' => $irhpApplication->canViewCandidatePermits(),
                'canSelectCandidatePermits' => $irhpApplication->canSelectCandidatePermits(),
                'totalPermitsAwarded' => $totalPermitsAwarded,
                'totalPermitsRequired' => $totalPermitsRequired,
            ]
        );
    }

    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return ById
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

        $this->daysToPayIssueFeeProvider = $container->get('PermitsFeesDaysToPayIssueFeeProvider');
        return parent::__invoke($fullContainer, $requestedName, $options);
    }
}

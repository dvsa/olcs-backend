<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\IrhpApplication;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication;
use Dvsa\Olcs\Api\Service\Permits\Availability\StockAvailabilityChecker;
use Dvsa\Olcs\Transfer\Query\IrhpApplication\PermitsAvailable as PermitsAvailableQuery;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * PermitsAvailable
 */
class PermitsAvailable extends AbstractQueryHandler
{
    /** @var StockAvailabilityChecker */
    private $stockAvailabilityChecker;

    protected $repoServiceName = 'IrhpApplication';

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
        return $this->__invoke($serviceLocator, PermitsAvailable::class);
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
        /** @var IrhpApplication $irhpApplication */
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

    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return PermitsAvailable
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        if (method_exists($container, 'getServiceLocator') && $container->getServiceLocator()) {
            $fullContainer = $container;
            $container = $container->getServiceLocator();
        }

        $this->stockAvailabilityChecker = $container->get('PermitsAvailabilityStockAvailabilityChecker');
        return parent::__invoke($fullContainer, $requestedName, $options);
    }
}

<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Permits;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Service\Permits\Availability\StockLicenceMaxPermittedCounter;
use Dvsa\Olcs\Transfer\Query\Permits\MaxPermittedReachedByStockAndLicence as MaxPermittedReachedByStockAndLicenceQuery;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Max permitted reached by stock and licence
 */
class MaxPermittedReachedByStockAndLicence extends AbstractQueryHandler
{
    protected $repoServiceName = 'IrhpPermitStock';

    protected $extraRepos = ['Licence'];

    /** @var StockLicenceMaxPermittedCounter */
    private $stockLicenceMaxPermittedCounter;

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
        return $this->__invoke($serviceLocator, MaxPermittedReachedByStockAndLicence::class);
    }

    /**
     * Handle query
     *
     * @param QueryInterface|MaxPermittedReachedByStockAndLicenceQuery $query query
     *
     * @return array
     */
    public function handleQuery(QueryInterface $query)
    {
        $irhpPermitStock = $this->getRepo()->fetchById(
            $query->getIrhpPermitStock()
        );

        $irhpPermitType = $irhpPermitStock->getIrhpPermitType();

        if (!$irhpPermitType->isEcmtAnnual()) {
            // test is only applicable to ecmt annual for now, return false for other types
            return $this->generateResponse(false);
        }

        $licence = $this->getRepo('Licence')->fetchById(
            $query->getLicence()
        );

        $count = $this->stockLicenceMaxPermittedCounter->getCount($irhpPermitStock, $licence);

        return $this->generateResponse($count < 1);
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

    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return MaxPermittedReachedByStockAndLicence
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

        $this->stockLicenceMaxPermittedCounter = $container->get(
            'PermitsAvailabilityStockLicenceMaxPermittedCounter'
        );
        return parent::__invoke($fullContainer, $requestedName, $options);
    }
}

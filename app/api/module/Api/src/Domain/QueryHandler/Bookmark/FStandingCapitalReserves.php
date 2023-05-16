<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Bookmark;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * FStanding Capital Reserves Bookmark
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class FStandingCapitalReserves extends AbstractQueryHandler
{
    protected $repoServiceName = 'Application';

    protected $extraRepos = ['Organisation'];

    /**
     * @var \Dvsa\Olcs\Api\Service\FinancialStandingHelperService $helper
     */
    protected $helper;

    /**
     * Factory create
     *
     * @param ServiceLocatorInterface $serviceLocator Service manager
     *
     * @return FStandingCapitalReserves
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function createService(ServiceLocatorInterface $serviceLocator, $name = null, $requestedName = null)
    {
        $this->helper = $serviceLocator->getServiceLocator()->get('FinancialStandingHelperService');
        return $this;
    }

    /**
     * For an operator, get the required finance based on:
     *  - any new applications where the status is Under consideration or Granted; PLUS
     *  - any licences where the status is 'Valid' or 'Suspended' or 'Curtailed'
     */
    public function handleQuery(QueryInterface $query)
    {
        return $this->helper->getFinanceCalculationForOrganisation($query->getOrganisation());
    }

    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return FStandingCapitalReserves
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $fullContainer = $container;

        if (method_exists($container, 'getServiceLocator') && $container->getServiceLocator()) {
            $container = $container->getServiceLocator();
        }

        $this->helper = $container->get('FinancialStandingHelperService');
        return parent::__invoke($fullContainer, $requestedName, $options);
    }
}

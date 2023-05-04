<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Application;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Application - Outstanding Fees
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class OutstandingFees extends AbstractQueryHandler
{
    protected $repoServiceName = 'Application';

    protected $extraRepos = ['SystemParameter'];

    /**
     * @var \Dvsa\Olcs\Api\Service\FeesHelperService
     */
    private $feesHelper;

    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @param $name
     * @param $requestedName
     * @return OutstandingFees
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function createService(ServiceLocatorInterface $serviceLocator, $name = null, $requestedName = null)
    {
        return $this->__invoke($serviceLocator, OutstandingFees::class);
    }

    public function handleQuery(QueryInterface $query)
    {
        $application = $this->getRepo()->fetchUsingId($query);
        $fees = $this->feesHelper->getOutstandingFeesForApplication($application->getId());

        return $this->result(
            $application,
            [],
            [
                'outstandingFees' => $this->resultList(
                    $fees,
                    [
                        'licence',
                        'feeTransactions' => [
                            'transaction'
                        ],
                        'feeType' => [
                            'feeType'
                        ]
                    ]
                ),
                'disableCardPayments' => $this->getRepo('SystemParameter')->getDisableSelfServeCardPayments(),
            ]
        );
    }

    /**
     * @param ContainerInterface $container
     * @param $requestedName
     * @param array|null $options
     * @return OutstandingFees
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        if (method_exists($container, 'getServiceLocator') && $container->getServiceLocator()) {
            $fullContainer = $container;
            $container = $container->getServiceLocator();
        }

        $this->feesHelper = $container->get('FeesHelperService');
        return parent::__invoke($fullContainer, $requestedName, $options);
    }
}

<?php

/**
 * Application - Outstanding Fees
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\Application;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

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

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return $this($serviceLocator, self::class);
    }

    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $this->feesHelper = $container->get('FeesHelperService');
        return parent::__invoke($container, $requestedName, $options);
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
}

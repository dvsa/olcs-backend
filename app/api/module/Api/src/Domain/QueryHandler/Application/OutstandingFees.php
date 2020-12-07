<?php

/**
 * Application - Outstanding Fees
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\Application;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Laminas\ServiceManager\ServiceLocatorInterface;

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
        $mainServiceLocator = $serviceLocator->getServiceLocator();
        $this->feesHelper = $mainServiceLocator->get('FeesHelperService');

        return parent::createService($serviceLocator);
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

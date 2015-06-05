<?php

/**
 * Organisation - Outstanding Fees
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\Organisation;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Organisation - Outstanding Fees
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class OutstandingFees extends AbstractQueryHandler
{
    protected $repoServiceName = 'Organisation';

    protected $feeRepo;

    public function handleQuery(QueryInterface $query)
    {
        $organisation = $this->getRepo()->fetchUsingId($query);
        $data = $organisation->jsonSerialize();

        $fees = $this->feeRepo->fetchOutstandingFeesByOrganisationId($organisation->getId());
        $data['outstandingFees'] = $fees;

        return $data;
    }


    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        parent::createService($serviceLocator);
        $serviceLocator = $serviceLocator->getServiceLocator();

        $this->feeRepo = $serviceLocator->get('RepositoryServiceManager')->get('Fee');

        return $this;
    }
}

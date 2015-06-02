<?php

/**
 * Financial Evidence
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\Application;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Doctrine\ORM\Query;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Financial Evidence
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class FinancialEvidence extends AbstractQueryHandler
{
    protected $repoServiceName = 'Application';

    public function handleQuery(QueryInterface $query)
    {
        $applicationRepo = $this->getRepo();
        $application = $applicationRepo->fetchUsingId($query, Query::HYDRATE_OBJECT);

        // calculate required finance
        $rates = $this->ratesRepo->getRatesInEffect(new \DateTime()); // @TODO

        $data = $application->jsonSerialize();

        return $data;
    }

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        parent::createService($serviceLocator);

        $mainServiceLocator = $serviceLocator->getServiceLocator();
        $this->ratesRepo = $mainServiceLocator->get('RepositoryServiceManager')->get('FinancialStandingRate');

        return $this;
    }
}

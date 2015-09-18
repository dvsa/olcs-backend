<?php

/**
 * Fee
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\Fee;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Dvsa\Olcs\Api\Domain\Repository\Fee as FeeRepo;
use Doctrine\ORM\Query as DoctrineQuery;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Fee
 */
class FeeList extends AbstractQueryHandler
{
    /**
     * @var \Dvsa\Olcs\Api\Service\FeesHelperService
     */
    protected $feesHelper;

    protected $repoServiceName = 'Fee';

    protected $extraRepos = ['Licence', 'Application'];

    public function handleQuery(QueryInterface $query)
    {
        /** @var FeeRepo $repo */
        $repo = $this->getRepo();

        $fees = $repo->fetchList($query, DoctrineQuery::HYDRATE_OBJECT);

        return [
            'result' => $this->resultList($fees),
            'count' => $repo->fetchCount($query),
            'allowFeePayments' => $this->shouldAllowFeePayments($query),
            'minPayment' => $this->feesHelper->getMinPaymentForFees($fees->getArrayCopy()),
            'totalOutstanding' => $this->feesHelper->getTotalOutstanding($fees->getArrayCopy()),
        ];
    }

    private function shouldAllowFeePayments(QueryInterface $query)
    {
        if (!is_null($query->getLicence())) {
            $licence = $this->getRepo('Licence')->fetchById($query->getLicence());
            return $licence->allowFeePayments();
        }

        if (!is_null($query->getApplication())) {
            $application = $this->getRepo('Application')->fetchById($query->getApplication());
            return $application->allowFeePayments();
        }

        return true;
    }

    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        parent::createService($serviceLocator);
        $this->feesHelper = $serviceLocator->getServiceLocator()->get('FeesHelperService');
        return $this;
    }
}

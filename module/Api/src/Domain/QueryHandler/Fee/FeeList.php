<?php

/**
 * Fee
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\Fee;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Dvsa\Olcs\Api\Domain\Repository\Fee as FeeRepo;
use Doctrine\ORM\Query as DoctrineQuery;

/**
 * Fee
 */
class FeeList extends AbstractQueryHandler
{
    protected $repoServiceName = 'Fee';

    protected $extraRepos = ['Licence', 'Application'];

    public function handleQuery(QueryInterface $query)
    {
        /** @var FeeRepo $repo */
        $repo = $this->getRepo();

        return [
            'result' => $this->resultList($repo->fetchList($query, DoctrineQuery::HYDRATE_OBJECT)),
            'count' => $repo->fetchCount($query),
            'allowFeePayments' => $this->shouldAllowFeePayments($query),
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
}

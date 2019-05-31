<?php


namespace Dvsa\Olcs\Api\Domain\QueryHandler\Fee;


use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

class InterimRefunds extends AbstractQueryHandler
{


    protected $repoServiceName = 'Fee';

    public function handleQuery(QueryInterface $query)
    {
        /**
         * @var  $repo \Dvsa\Olcs\Api\Domain\Repository\Fee
         */
        $repo = $this->getRepo();

        /**
         * @var $query \Dvsa\Olcs\Transfer\Query\Fee\InterimRefunds
         */

        $after = $query->getStartDate();
        $before = $query->getEndDate();
        $trafficArea = $query->getTrafficArea();

        $refunds = $repo->fetchInterimRefunds($after, $before, $trafficArea);

        return [
            'result' => $this->resultList($refunds)
        ];
    }
}
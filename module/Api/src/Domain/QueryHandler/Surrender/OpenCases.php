<?php


namespace Dvsa\Olcs\Api\Domain\QueryHandler\Surrender;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

class OpenCases extends AbstractQueryHandler
{
    protected $repoServiceName = 'Cases';

    public function handleQuery(QueryInterface $query)
    {
        /**
         * @var \Dvsa\Olcs\Api\Domain\Repository\Cases
         */
        $cases = $this->getRepo()->fetchOpenCasesForSurrender($query);


            return [
                'count' => count($cases),
                'results' => $this->resultList($cases)
            ];
    }
}

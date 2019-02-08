<?php


namespace Dvsa\Olcs\Api\Domain\QueryHandler\Cases;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

class BySurrenderedLicence extends AbstractQueryHandler
{
    protected $repoServiceName = 'Cases';

    public function handleQuery(QueryInterface $query)
    {
        /**
         * @var \Dvsa\Olcs\Api\Domain\Repository\Cases
         */
        $cases = $this->getRepo()->fetchOpenCasesForSurrender($query);
        return $this->result($cases, []);
    }
}

<?php

/**
 * Fee
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\Fee;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Dvsa\Olcs\Api\Domain\Repository\Fee as FeeRepository;

/**
 * Fee
 */
class Fee extends AbstractQueryHandler
{
    protected $repoServiceName = 'Fee';

    public function handleQuery(QueryInterface $query)
    {
        /** @var FeeRepository $repo */
        $repo = $this->getRepo();

        $fee = $repo->fetchUsingId($query);

        return $this->result(
            $fee,
            [],
            [
                'allowEdit' => $fee->allowEdit(),
            ]
        );
    }
}

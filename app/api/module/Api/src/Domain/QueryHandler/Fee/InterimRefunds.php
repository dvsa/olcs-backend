<?php


namespace Dvsa\Olcs\Api\Domain\QueryHandler\Fee;


use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

class InterimRefunds extends AbstractQueryHandler
{

    /**
     * @var  \Dvsa\Olcs\Api\Domain\Repository\Fee
     */
    protected $repository = 'Fee';

    public function handleQuery(QueryInterface $query)
    {



    }
}
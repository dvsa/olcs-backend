<?php

/**
 * IrfoDetails
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\Irfo;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * IrfoDetails
 */
class IrfoDetails extends AbstractQueryHandler
{
    protected $repoServiceName = 'Organisation';

    public function handleQuery(QueryInterface $query)
    {
        return $this->result(
            $this->getRepo()->fetchIrfoDetailsUsingId($query),
            [
                'tradingNames',
                'irfoNationality',
                'irfoPartners',
                'irfoContactDetails'
            ]
        );
    }
}

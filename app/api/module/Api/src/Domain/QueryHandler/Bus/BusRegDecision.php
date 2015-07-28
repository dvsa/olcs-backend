<?php

/**
 * Bus Reg Decision
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\Bus;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * Bus Reg Decision
 */
class BusRegDecision extends AbstractQueryHandler
{
    protected $repoServiceName = 'Bus';

    protected $extraRepos = ['Fee'];

    public function handleQuery(QueryInterface $query)
    {
        $busReg = $this->getRepo()->fetchUsingId($query);

        return $this->result(
            $busReg,
            [],
            [
                'decision' => $busReg->getDecision(),
                'isGrantable' => $busReg->isGrantable(
                    $this->getRepo('Fee')->getLatestFeeForBusReg($query->getId())
                )
            ]
        );
    }
}

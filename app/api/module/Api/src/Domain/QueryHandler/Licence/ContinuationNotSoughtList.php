<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Licence;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * Gets a list of licences ready to go Continuation Not Sought
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class ContinuationNotSoughtList extends AbstractQueryHandler
{
    protected $repoServiceName = 'Licence';

    public function handleQuery(QueryInterface $query)
    {
        $results = $this->resultList(
            $this->getRepo()->fetchForContinuationNotSought($query->getDate()),
            ['trafficArea']
        );

        return [
            'result' => $results,
            'count' => count($results),
        ];
    }
}

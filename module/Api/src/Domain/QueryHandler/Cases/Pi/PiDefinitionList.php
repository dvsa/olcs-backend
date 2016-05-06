<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Cases\Pi;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Dvsa\Olcs\Api\Domain\Repository\PiDefinition as PiDefinitionRepo;

/**
 * Pi Definition List QueryHandler
 */
final class PiDefinitionList extends AbstractQueryHandler
{
    protected $repoServiceName = 'PiDefinition';

    public function handleQuery(QueryInterface $query)
    {
        /** @var PiDefinitionRepo $repo */
        $repo = $this->getRepo();
        $results = $repo->fetchUnpaginatedList($query, Query::HYDRATE_OBJECT);

        return [
            'result' => $this->resultList(
                $results
            ),
            'count' => count($results)
        ];
    }
}

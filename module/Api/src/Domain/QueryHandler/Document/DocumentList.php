<?php

/**
 * Document List
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\Document;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * Document List
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class DocumentList extends AbstractQueryHandler
{
    protected $repoServiceName = 'DocumentSearchView';

    public function handleQuery(QueryInterface $query)
    {
        $data = $query->getArrayCopy();

        unset($data['category']);
        unset($data['documentSubCategory']);
        unset($data['isExternal']);

        $unfilteredQuery = \Dvsa\Olcs\Transfer\Query\Document\DocumentList::create($data);

        return [
            'result' => $this->getRepo()->fetchList($query),
            'count' => $this->getRepo()->fetchCount($query),
            'count-unfiltered' => $this->getRepo()->hasRows($unfilteredQuery)
        ];
    }
}

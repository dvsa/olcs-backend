<?php

/**
 * Document List
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\Document;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Doctrine\ORM\Query;

/**
 * Document List
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class DocumentList extends AbstractQueryHandler
{
    protected $repoServiceName = 'DocumentSearchView';

    /**
     * Handle query
     *
     * @param QueryInterface $query query
     *
     * @return array
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     */
    public function handleQuery(QueryInterface $query)
    {
        $data = $query->getArrayCopy();

        unset($data['category']);
        unset($data['documentSubCategory']);
        unset($data['isExternal']);

        $unfilteredQuery = \Dvsa\Olcs\Transfer\Query\Document\DocumentList::create($data);

        return [
            'result' => $this->resultList($this->getRepo()->fetchList($query, Query::HYDRATE_OBJECT)),
            'count' => $this->getRepo()->fetchCount($query),
            'count-unfiltered' => $this->getRepo()->hasRows($unfilteredQuery)
        ];
    }
}

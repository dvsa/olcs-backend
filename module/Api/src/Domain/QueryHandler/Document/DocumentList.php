<?php

namespace Dvsa\Olcs\Api\Domain\QueryHandler\Document;

use Doctrine\ORM\Query;
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

    /**
     * Handle query
     *
     * @param \Dvsa\Olcs\Transfer\Query\Document\DocumentList $query Query
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
        unset($data['showDocs']);
        unset($data['format']);

        $unfilteredQuery = \Dvsa\Olcs\Transfer\Query\Document\DocumentList::create($data);

        /** @var  \Dvsa\Olcs\Api\Domain\Repository\DocumentSearchView $repo */
        $repo = $this->getRepo();

        return [
            'result' => $this->resultList(
                $repo->fetchList($query, Query::HYDRATE_OBJECT)
            ),
            'count' => $repo->fetchCount($query),
            'count-unfiltered' => $repo->hasRows($unfilteredQuery),
            'extensionList' => $repo->fetchDistinctListExtensions($unfilteredQuery),
        ];
    }
}

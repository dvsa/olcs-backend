<?php

/**
 * TmQualifications Documents List
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\TmQualification;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * TmQualifications Documents List
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class Documents extends AbstractQueryHandler
{
    protected $repoServiceName = 'Document';

    public function handleQuery(QueryInterface $query)
    {
        return [
            'result' => $this->getRepo()->fetchList($query),
            'count' =>   $this->getRepo()->fetchCount($query)
        ];
    }
}

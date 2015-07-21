<?php

/**
 * TmQualifications List
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\TmQualification;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * TmQualifications List
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class TmQualificationsList extends AbstractQueryHandler
{
    protected $repoServiceName = 'TmQualification';
    protected $extraRepos = ['Document'];

    public function handleQuery(QueryInterface $query)
    {
        $documents = $this->getRepo('Document')->fetchListForTm($query->getTransportManager());
        return [
            'result'    => $this->getRepo()->fetchList($query),
            'count'     => $this->getRepo()->fetchCount($query),
            'documents' => $documents
        ];
    }
}

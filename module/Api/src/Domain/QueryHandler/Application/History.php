<?php

/**
 * History
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\Application;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Api\Entity;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * History
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class History extends AbstractQueryHandler
{
    protected $repoServiceName = 'ApplicationReadAudit';

    public function handleQuery(QueryInterface $query)
    {
        return [
            'results' => $this->resultList(
                $this->getRepo()->fetchList($query, Query::HYDRATE_OBJECT),
                [
                    'user' => [
                        'contactDetails' => [
                            'person'
                        ]
                    ]
                ],
                [
                    'eventHistoryType' => ['description' => 'Read'],
                    'eventData' => 'Not applicable'
                ]
            ),
            'count' => $this->getRepo()->fetchCount($query),
        ];
    }
}

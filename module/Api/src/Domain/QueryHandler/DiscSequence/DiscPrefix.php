<?php

/**
 * Disc Sequence
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\DiscSequence;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * Disc Prefix
 */
class DiscPrefix extends AbstractQueryHandler
{
    protected $repoServiceName = 'DiscSequence';

    public function handleQuery(QueryInterface $query)
    {
        $discSequence = $this->getRepo()->fetchById($query->getDiscSequence());
        return [
            'result' => ['discPrefix' => $discSequence->getDiscPrefix($query->getLicenceType())],
            'count'  => 1
        ];
    }
}

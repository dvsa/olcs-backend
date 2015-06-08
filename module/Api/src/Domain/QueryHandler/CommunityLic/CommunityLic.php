<?php

/**
 * CommunityLic
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\CommunityLic;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Dvsa\Olcs\Api\Domain\Repository\CommunityLicRepo;

/**
 * CommunityLic
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class CommunityLic extends AbstractQueryHandler
{
    protected $repoServiceName = 'CommunityLic';

    public function handleQuery(QueryInterface $query)
    {
        /** @var CommunityLicRepo $repo */
        $repo = $this->getRepo();

        return [
            'result' => $repo->fetchList($query),
            'count' => $repo->fetchCount($query),
        ];
    }
}

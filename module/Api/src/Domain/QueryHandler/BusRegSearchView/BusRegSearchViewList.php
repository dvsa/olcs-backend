<?php

/**
 * BusReg Search View List
 *
 * @author Craig R <uk@valtech.co.uk>, Shaun Lizzio <shaun@lizzio.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\BusRegSearchView;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Dvsa\Olcs\Api\Domain\Repository\BusRegSearchView as Repository;
use Dvsa\Olcs\Transfer\Query\Bus\SearchViewList as ListQueryObject;
use Doctrine\ORM\Query as DoctrineQuery;
use Olcs\Logging\Log\Logger;

/**
 * BusReg Search View List
 *
 * @author Craig R <uk@valtech.co.uk>
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
class BusRegSearchViewList extends AbstractQueryHandler
{
    protected $repoServiceName = 'BusRegSearchView';

    /**
     * @return array
     */
    public function handleQuery(QueryInterface $query)
    {
        /** @var Repository $repo */
        $repo = $this->getRepo();

        if ($this->isOperator()) {
            $query->setOrganisationId($this->getCurrentOrganisation()->getId());
        }

        return [
            'result' => $this->resultList(
                $repo->fetchList($query, DoctrineQuery::HYDRATE_OBJECT)
            ),
            'count' => $repo->fetchCount($query)
        ];
    }
}

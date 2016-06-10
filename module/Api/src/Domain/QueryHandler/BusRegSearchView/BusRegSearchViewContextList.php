<?php

/**
 * BusRegSearchView List
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\BusRegSearchView;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;
use Dvsa\Olcs\Api\Domain\Repository\BusRegSearchView as Repository;
use Dvsa\Olcs\Transfer\Query\Bus\SearchViewList as ListQueryObject;
use Doctrine\ORM\Query as DoctrineQuery;
use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareTrait;

/**
 * BusRegSearchView List
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
class BusRegSearchViewContextList extends AbstractQueryHandler implements AuthAwareInterface
{
    use AuthAwareTrait;

    protected $repoServiceName = 'BusRegSearchView';

    /**
     * Returns a distinct list of column entries identified by query->getContext().
     * Used to populate filter form drop down lists.
     *
     * @return array
     */
    public function handleQuery(QueryInterface $query)
    {
        /** @var Repository $repo */
        $repo = $this->getRepo();

        $organisationId = null;

        if ($this->isOperator() ) {
            // fetch for Organisation
            $organisationId = $this->getCurrentOrganisation()->getId();
        }

        $results = $repo->fetchDistinctList($query, $organisationId);

        return [
            'result' => $results,
            'count' => count($results)
        ];
    }
}

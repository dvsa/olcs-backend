<?php

/**
 * Licence
 *
 * @author Joshua Curtis <josh.curtis@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\Trailer;

use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * Licence
 *
 * @author Joshua Curtis <josh.curtis@valtech.co.uk>
 */
class Trailers extends AbstractQueryHandler
{
    protected $repoServiceName = 'Trailer';

    public function handleQuery(QueryInterface $query)
    {
        /** @var Licence $repo */
        $repo = $this->getRepo();

        return [
            'result' => $repo->fetchByLicenceId($query),
            'count' => $repo->fetchCount($query)
        ];
    }
}

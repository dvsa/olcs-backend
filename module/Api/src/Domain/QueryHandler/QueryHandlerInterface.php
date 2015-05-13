<?php

/**
 * Query Handler Interface
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler;

use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * Query Handler Interface
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
interface QueryHandlerInterface
{
    public function handleQuery(QueryInterface $query);
}

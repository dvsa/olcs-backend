<?php

/**
 * Next Item Queue Query Handler
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler\Queue;

use Dvsa\Olcs\Api\Domain\Exception\NotFoundException;
use Dvsa\Olcs\Api\Domain\QueryHandler\AbstractQueryHandler;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * Next Item Queue Query Handler
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class NextItem extends AbstractQueryHandler
{
    protected $repoServiceName = 'Queue';

    public function handleQuery(QueryInterface $query)
    {
        try {
            $entity = $this->getRepo()->getNextItem($query->getType());
        } catch (NotFoundException $ex) {
            return null;
        }

        return $entity;
    }
}

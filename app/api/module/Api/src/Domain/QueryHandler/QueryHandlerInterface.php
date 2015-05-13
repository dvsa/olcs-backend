<?php

/**
 * Query Handler Interface
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\QueryHandler;

use Zend\Stdlib\ArraySerializableInterface;

/**
 * Query Handler Interface
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
interface QueryHandlerInterface
{
    public function handleQuery(ArraySerializableInterface $query);
}

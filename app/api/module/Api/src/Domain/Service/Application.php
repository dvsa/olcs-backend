<?php

/**
 * Application
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Service;

use Zend\Stdlib\ArraySerializableInterface;
use Dvsa\Olcs\Transfer\Query\Application as ApplicationQueries;

/**
 * Application
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class Application extends AbstractService
{
    /**
     * Handles all query objects passed to the service
     *
     * @param ArraySerializableInterface $query
     */
    public function handleQuery(ArraySerializableInterface $query)
    {
        switch (true) {
            case ($query instanceof ApplicationQueries\Application):
                return $this->getRepo()->fetchUsingId($query);
        }
    }
}

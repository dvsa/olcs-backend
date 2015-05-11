<?php

/**
 * Service Interface
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Service;

use Zend\Stdlib\ArraySerializableInterface;

/**
 * Service Interface
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
interface ServiceInterface
{
    /**
     * Handles all query objects passed to the service
     *
     * @param ArraySerializableInterface $query
     */
    public function handleQuery(ArraySerializableInterface $query);

    /**
     * Handles all commands objects passed to the service
     *
     * @param ArraySerializableInterface $command
     */
    public function handleCommand(ArraySerializableInterface $command);
}

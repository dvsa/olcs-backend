<?php

/**
 * BusRegHistory view repo
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Entity\View\BusRegHistoryView as Entity;
use Dvsa\Olcs\Api\Domain\Exception;
use Zend\Stdlib\ArraySerializableInterface as QryCmd;
use Doctrine\ORM\Query;

/**
 * BusRegHistory view repo
 */
class BusRegHistory extends AbstractRepository
{
    protected $entity = Entity::class;

    public function save($entity)
    {
        throw \Exception('You cannot save to a view');
    }

    public function delete($entity)
    {
        throw \Exception('You delete the contents of a view');
    }
}

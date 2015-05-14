<?php

/**
 * Task
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Exception;
use Zend\Stdlib\ArraySerializableInterface as QryCmd;

/**
 * Task
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class Task extends AbstractRepository
{
    protected $entity = '\Dvsa\Olcs\Api\Entity\Task\Task';
}

<?php

/**
 * IrhpCandidatePermit
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Exception;
use Zend\Stdlib\ArraySerializableInterface as QryCmd;
use Dvsa\Olcs\Api\Entity\Permits\IrhpCandidatePermit as Entity;
use Doctrine\ORM\QueryBuilder;
use Dvsa\Olcs\Transfer\Query\QueryInterface;

/**
 * IrhpCandidatePermit
 */
class IrhpCandidatePermit extends AbstractRepository
{
    protected $entity = Entity::class;
}

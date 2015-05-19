<?php

namespace Dvsa\Olcs\Api\Domain\Repository;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Exception;
use Zend\Stdlib\ArraySerializableInterface as QryCmd;

final class Sla extends AbstractRepository
{
    protected $entity = '\Dvsa\Olcs\Api\Entity\System\Sla';

    public function fetchByCategory($category)
    {
        $qb = $this->createQueryBuilder();
        $qb->where($qb->expr()->eq($this->alias . '.category', ':category'));
        $qb->setParameter('category', $category);

        return $qb->getQuery()->getResult();
    }
}

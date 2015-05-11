<?php

/**
 * Entity Manager Helper
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Olcs\Db\Service\EntityManager;

use Doctrine\ORM\Query;

/**
 * Entity Manager Helper
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class EntityManagerHelper implements EntityManagerAwareInterface
{
    use EntityManagerAwareTrait;

    public function findByIds($entityName, array $ids, $hydrateMode = Query::HYDRATE_OBJECT)
    {
        $qb = $this->getEntityManager()->createQueryBuilder();

        $qb->select('m')
            ->from($entityName, 'm')
            ->where($qb->expr()->in('m.id', $ids));

        return $qb->getQuery()->execute(null, $hydrateMode);
    }

    public function getRefDataReference($id)
    {
        return $this->getEntityManager()->getReference('\Olcs\Db\Entity\RefData', $id);
    }
}

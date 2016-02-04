<?php

/**
 * Team
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Entity\User\Team as Entity;

/**
 * Team
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class Team extends AbstractRepository
{
    protected $entity = Entity::class;

    /**
     * Fetch by name
     *
     * @param string $name
     *
     * @return array
     */
    public function fetchByName($name)
    {
        $qb = $this->createQueryBuilder();

        $qb->andWhere($qb->expr()->eq($this->alias . '.name', ':name'))
            ->setParameter('name', $name);

        return $qb->getQuery()->getResult();
    }

    public function fetchWithPrinters($id, $type)
    {
        $qb = $this->createQueryBuilder();
        $this->buildDefaultQuery($qb, $id)
            ->with('teamPrinters', 'tp')
            ->with('tp.printer', 'tpp')
            ->with('tp.user', 'pu')
            ->with('tp.subCategory', 'ps');

        return $qb->getQuery()->getSingleResult($type);
    }
}

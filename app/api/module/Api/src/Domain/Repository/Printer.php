<?php

/**
 * Printer
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Entity\PrintScan\Printer as Entity;

/**
 * Printer
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class Printer extends AbstractRepository
{
    protected $entity = Entity::class;

    public function fetchWithTeams($id)
    {
        $qb = $this->createQueryBuilder();
        $this->buildDefaultQuery($qb, $id)
            ->with('teams');

        return $qb->getQuery()->getSingleResult();
    }
}

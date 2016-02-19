<?php

/**
 * VI Operating Centre repo
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Entity\View\ViOcView as Entity;
use Doctrine\ORM\Query;

/**
 * VI Operating Centre repo
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class ViOcView extends AbstractRepository
{
    protected $entity = Entity::class;

    /**
     * Fetch all records for export
     *
     * @return array
     */
    public function fetchForExport()
    {
        $qb = $this->createQueryBuilder()
            ->select($this->alias . '.placeholder as line');

        return $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);
    }
}

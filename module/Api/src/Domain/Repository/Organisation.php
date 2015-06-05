<?php

/**
 * Organisation
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Domain\Exception\NotFoundException;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation as Entity;
use Zend\Stdlib\ArraySerializableInterface as QryCmd;
use Doctrine\ORM\Query;

/**
 * Organisation
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class Organisation extends AbstractRepository
{
    protected $entity = Entity::class;

    public function fetchBusinessDetailsUsingId(QryCmd $query, $hydrateMode = Query::HYDRATE_OBJECT)
    {
        return $this->fetchBusinessDetailsById($query->getId());
    }

    public function fetchBusinessDetailsById($id, $hydrateMode = Query::HYDRATE_OBJECT)
    {
        $qb = $this->createQueryBuilder();

        $this->buildDefaultQuery($qb, $id)->withContactDetails();

        $results = $qb->getQuery()->getResult($hydrateMode);

        if (empty($results)) {
            throw new NotFoundException('Organisation not found');
        }

        return $results[0];
    }
}

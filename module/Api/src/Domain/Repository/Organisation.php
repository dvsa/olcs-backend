<?php

/**
 * Organisation
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Doctrine\Common\Util\Debug;
use Dvsa\Olcs\Api\Domain\Exception\NotFoundException;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation as Entity;
use Zend\Stdlib\ArraySerializableInterface as QryCmd;
use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails;

/**
 * Organisation
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class Organisation extends AbstractRepository
{
    protected $entity = Entity::class;

    public function fetchBusinessTypeUsingId(QryCmd $query, $hydrateMode = Query::HYDRATE_OBJECT)
    {
        return $this->fetchBusinessTypeById($query->getId());
    }

    public function fetchBusinessTypeById($id, $hydrateMode = Query::HYDRATE_OBJECT)
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

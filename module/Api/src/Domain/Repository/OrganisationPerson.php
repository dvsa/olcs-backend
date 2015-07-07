<?php

/**
 * Organisation Person
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Entity\Organisation\Organisation as OrganisationEntity;
use Dvsa\Olcs\Api\Entity\Organisation\OrganisationPerson as Entity;
use Dvsa\Olcs\Api\Entity\Person\Person as PersonEntity;

/**
 * Organisation Person
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class OrganisationPerson extends AbstractRepository
{
    protected $entity = Entity::class;

    public function fetchByOrgAndPerson(OrganisationEntity $organisation, PersonEntity $person)
    {
        $qb = $this->createQueryBuilder();

        $qb->andWhere(
            $qb->expr()->eq('m.organisation', $organisation)
        );

        $qb->andWhere(
            $qb->expr()->eq('m.person', $person)
        );

        $query = $qb->getQuery();
        $query->execute();

        return $query->getResult();
    }
}

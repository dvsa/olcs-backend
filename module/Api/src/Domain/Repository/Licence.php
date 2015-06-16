<?php

/**
 * Licence
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Exception;
use Dvsa\Olcs\Api\Entity\Licence\Licence as Entity;
use Dvsa\Olcs\Api\Entity\ContactDetails\PhoneContact;

/**
 * Licence
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class Licence extends AbstractRepository
{
    protected $entity = Entity::class;

    public function fetchByCaseId($caseId, $hydrateMode = Query::HYDRATE_ARRAY, $version = null)
    {
        $qb = $this->createQueryBuilder();

        $this->getQueryBuilder()->modifyQuery($qb)->withRefdata()->with('trafficArea', 'ta');
        $qb->innerJoin($this->alias . '.cases', 'c');

        $qb->andWhere($qb->expr()->eq('c.id', ':caseId'));
        $qb->setParameter('caseId', $caseId);

        $results = $qb->getQuery()->getResult($hydrateMode);

        if (empty($results)) {
            throw new Exception\NotFoundException('Resource not found');
        }

        if ($hydrateMode === Query::HYDRATE_OBJECT && $version !== null) {
            $this->lock($results[0], $version);
        }

        return $results[0];
    }


    public function fetchWithAddressesUsingId($query)
    {
        $qb = $this->createQueryBuilder();

        $this->buildDefaultQuery($qb, $query->getId())
            ->withContactDetails('correspondenceCd', 'c')
            ->with('c.phoneContacts', 'c_p')
            ->with('c_p.phoneContactType', 'c_p_pct')
            ->withRefData(PhoneContact::class, 'c_p')
            ->withContactDetails('establishmentCd', 'e')
            ->withContactDetails('transportConsultantCd', 't')
            ->with('t.phoneContacts', 't_p')
            ->with('t_p.phoneContactType', 't_p_pct')
            ->withRefData(PhoneContact::class, 't_p');

        return $qb->getQuery()->getSingleResult();
    }

    public function fetchSafetyDetailsUsingId($command, $hydrateMode = Query::HYDRATE_OBJECT, $version = null)
    {
        return $this->fetchSafetyDetailsById($command->getId(), $hydrateMode, $version);
    }

    public function fetchSafetyDetailsById($id, $hydrateMode = Query::HYDRATE_OBJECT, $version = null)
    {
        $qb = $this->createQueryBuilder();

        $this->buildDefaultQuery($qb, $id)
            ->with('workshops', 'w')
            ->withContactDetails('w.contactDetails');

        $results = $qb->getQuery()->getResult($hydrateMode);

        if (empty($results)) {
            throw new Exception\NotFoundException('Resource not found');
        }

        if ($hydrateMode === Query::HYDRATE_OBJECT && $version !== null) {
            $this->lock($results[0], $version);
        }

        return $results[0];
    }
}

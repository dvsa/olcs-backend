<?php

/**
 * Workshop
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Entity\Licence\Workshop as Entity;
use Doctrine\ORM\QueryBuilder;

/**
 * Workshop
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class Workshop extends AbstractRepository
{
    protected $entity = Entity::class;

    /**
     * @NOTE This method can be overridden to extend the default resource bundle
     *
     * @param QueryBuilder $qb
     * @param int $id
     * @return \Dvsa\Olcs\Api\Domain\QueryBuilder
     */
    protected function buildDefaultQuery(QueryBuilder $qb, $id)
    {
        return parent::buildDefaultQuery($qb, $id)->withContactDetails();
    }

    /**
     * Fetch Workshops for a licence (with contact details and address)
     *
     * @param int $licenceId     Licence ID
     * @param int $hydrationMode Hydration mode Query::HYDRATE_* constant
     *
     * @return array
     */
    public function fetchForLicence($licenceId, $hydrateMode = Query::HYDRATE_OBJECT)
    {
        $qb = $this->createQueryBuilder();
        $this->getQueryBuilder()->modifyQuery($qb)
            ->withRefdata()
            ->with('contactDetails', 'cd')
            ->with('cd.address');

        $qb->where($qb->expr()->eq($this->alias . '.licence', ':licenceId'));
        $qb->setParameter('licenceId', $licenceId);

        return $qb->getQuery()->getResult($hydrateMode);
    }
}

<?php

/**
 * Licence Operating Centre Repository
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Entity\Licence\LicenceOperatingCentre as Entity;

/**
 * Licence Operating Centre Repository
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class LicenceOperatingCentre extends AbstractRepository
{
    protected $entity = Entity::class;
    protected $alias = 'loc';

    /**
     * Fetch a list of Licence Operating Centres for a Licence
     *
     * @param int $licenceId
     *
     * @return array
     */
    public function fetchByLicence($licenceId)
    {
        $dqb = $this->createQueryBuilder();
        $this->getQueryBuilder()->modifyQuery($dqb)
            ->withRefdata()
            ->with('operatingCentre', 'oc')
            ->with('oc.address');

        $dqb->andWhere($dqb->expr()->eq('loc.licence', ':licenceId'))
            ->setParameter('licenceId', $licenceId);

        return $dqb->getQuery()->getResult();
    }
}

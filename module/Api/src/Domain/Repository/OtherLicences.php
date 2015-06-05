<?php

/**
 * OtherLicences
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Dvsa\Olcs\Api\Entity\OtherLicence\OtherLicence as Entity;

/**
 * OtherLicences
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class OtherLicences extends AbstractRepository
{
    protected $entity = Entity::class;
    protected $alias = 'ol';

    /**
     * Fetch a list of Other Licences for a Transport Manager
     *
     * @param int $tmId
     *
     * @return array
     */
    public function fetchByTransportManager($tmId)
    {
        $dqb = $this->createQueryBuilder();
        $this->getQueryBuilder()->modifyQuery($dqb)
            ->withRefdata();

        $dqb->andWhere($dqb->expr()->eq('ol.transportManager', ':tmId'))
            ->setParameter('tmId', $tmId);

        return $dqb->getQuery()->getResult();
    }
}

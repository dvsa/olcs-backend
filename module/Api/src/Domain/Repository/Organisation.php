<?php

/**
 * Organisation
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Repository;

use Doctrine\Common\Collections\Criteria;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation as Entity;

/**
 * Organisation
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class Organisation extends AbstractRepository
{
    protected $entity = Entity::class;

    public function hasInforceLicences($id)
    {
        /** @var Entity $organisation */
        $organisation = $this->getEntityManager()->find($this->entity, $id);

        $criteria = Criteria::create();
        $criteria->where($criteria->expr()->neq('inForceDate', null));

        $licences = $organisation->getLicences()->matching($criteria);

        return !empty($licences);
    }
}

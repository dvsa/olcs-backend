<?php

namespace Dvsa\Olcs\Api\Entity\Licence;

use Doctrine\ORM\Mapping as ORM;
use Zend\Form\Element\DateTime;

/**
 * GracePeriod Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="grace_period",
 *    indexes={
 *        @ORM\Index(name="ix_grace_period_licence_id", columns={"licence_id"}),
 *        @ORM\Index(name="ix_grace_period_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_grace_period_last_modified_by", columns={"last_modified_by"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="uk_grace_period_olbs_key", columns={"olbs_key"})
 *    }
 * )
 */
class GracePeriod extends AbstractGracePeriod
{
    protected $isActive = false;

    public function isActive()
    {
        if (isset($this->startDate) && isset($this->endDate)) {
            /*
             * The dates should really be returned as date time objects from the entity
             * therefore this will need changing once that change is made.
             */
            $today = new \DateTime();
            $startDate = new \DateTime($this->getStartDate());
            $endDate = new \DateTime($this->getEndDate());

            if ($startDate <= $today && $endDate >= $today) {
                $this->isActive = true;
            }
        }

        return $this->isActive;
    }

    protected function getCalculatedValues()
    {
        return ['isActive' => $this->isActive()];
    }
}

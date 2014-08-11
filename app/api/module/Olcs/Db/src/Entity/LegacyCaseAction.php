<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * LegacyCaseAction Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="legacy_case_action")
 */
class LegacyCaseAction implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\Description45FieldAlt1;

    /**
     * Is driver
     *
     * @var unknown
     *
     * @ORM\Column(type="yesno", name="is_driver", nullable=false)
     */
    protected $isDriver = 0;


    /**
     * Set the is driver
     *
     * @param unknown $isDriver
     * @return LegacyCaseAction
     */
    public function setIsDriver($isDriver)
    {
        $this->isDriver = $isDriver;

        return $this;
    }

    /**
     * Get the is driver
     *
     * @return unknown
     */
    public function getIsDriver()
    {
        return $this->isDriver;
    }

}

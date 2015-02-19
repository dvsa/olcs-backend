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
        Traits\Description45Field,
        Traits\IdIdentity;

    /**
     * Is driver
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="is_driver", nullable=false, options={"default": 0})
     */
    protected $isDriver;

    /**
     * Set the is driver
     *
     * @param string $isDriver
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
     * @return string
     */
    public function getIsDriver()
    {
        return $this->isDriver;
    }
}

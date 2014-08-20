<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * S4 many to one trait
 *
 * Auto-Generated (Shared between 2 entities)
 */
trait S4ManyToOne
{
    /**
     * S4
     *
     * @var \Olcs\Db\Entity\S4
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\S4", fetch="LAZY")
     * @ORM\JoinColumn(name="s4_id", referencedColumnName="id", nullable=true)
     */
    protected $s4;

    /**
     * Set the s4
     *
     * @param \Olcs\Db\Entity\S4 $s4
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setS4($s4)
    {
        $this->s4 = $s4;

        return $this;
    }

    /**
     * Get the s4
     *
     * @return \Olcs\Db\Entity\S4
     */
    public function getS4()
    {
        return $this->s4;
    }
}

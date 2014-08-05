<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Disc no50 field trait
 *
 * Auto-Generated (Shared between 2 entities)
 */
trait DiscNo50Field
{
    /**
     * Disc no
     *
     * @var string
     *
     * @ORM\Column(type="string", name="disc_no", length=50, nullable=true)
     */
    protected $discNo;

    /**
     * Set the disc no
     *
     * @param string $discNo
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setDiscNo($discNo)
    {
        $this->discNo = $discNo;

        return $this;
    }

    /**
     * Get the disc no
     *
     * @return string
     */
    public function getDiscNo()
    {
        return $this->discNo;
    }
}

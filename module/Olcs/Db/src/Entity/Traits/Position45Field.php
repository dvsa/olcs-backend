<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Position45 field trait
 *
 * Auto-Generated (Shared between 2 entities)
 */
trait Position45Field
{
    /**
     * Position
     *
     * @var string
     *
     * @ORM\Column(type="string", name="position", length=45, nullable=true)
     */
    protected $position;

    /**
     * Set the position
     *
     * @param string $position
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get the position
     *
     * @return string
     */
    public function getPosition()
    {
        return $this->position;
    }
}

<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Name70 field trait
 *
 * Auto-Generated (Shared between 3 entities)
 */
trait Name70Field
{
    /**
     * Name
     *
     * @var string
     *
     * @ORM\Column(type="string", name="name", length=70, nullable=false)
     */
    protected $name;

    /**
     * Set the name
     *
     * @param string $name
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get the name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}

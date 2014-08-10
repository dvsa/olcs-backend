<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Name100 field trait
 *
 * Auto-Generated (Shared between 2 entities)
 */
trait Name100Field
{
    /**
     * Name
     *
     * @var string
     *
     * @ORM\Column(type="string", name="name", length=100, nullable=true)
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

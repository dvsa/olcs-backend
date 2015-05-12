<?php

namespace Dvsa\Olcs\Api\Entity\Pi;

use Doctrine\ORM\Mapping as ORM;

/**
 * PresidingTc Abstract Entity
 *
 * Auto-Generated
 *
 * @ORM\Table(name="presiding_tc")
 */
abstract class AbstractPresidingTc
{

    /**
     * Deleted
     *
     * @var string
     *
     * @ORM\Column(type="yesnonull", name="deleted", nullable=true, options={"default": 0})
     */
    protected $deleted = 0;

    /**
     * Identifier - Id
     *
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer", name="id")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * Name
     *
     * @var string
     *
     * @ORM\Column(type="string", name="name", length=70, nullable=false)
     */
    protected $name;

    /**
     * Set the deleted
     *
     * @param string $deleted
     * @return PresidingTc
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;

        return $this;
    }

    /**
     * Get the deleted
     *
     * @return string
     */
    public function getDeleted()
    {
        return $this->deleted;
    }

    /**
     * Set the id
     *
     * @param int $id
     * @return PresidingTc
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the name
     *
     * @param string $name
     * @return PresidingTc
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

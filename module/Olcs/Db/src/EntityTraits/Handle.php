<?php
namespace Olcs\Db\EntityTraits;

trait Handle
{
    /**
     * @var string
     *
     * @ORM\Column(name="handle", type="string", length=30, nullable=true, unique=true)
     */
    protected $handle;

    /**
     * Sets the handle property.
     *
     * @param string $handle
     *
     * @return \Olcs\Db\Entity\AbstractEntity
     */
    public function setHandle($handle)
    {
        $this->handle = $handle;
        return $this;
    }

    /**
     * Gets the value of the handle property.
     *
     * @return string
     */
    public function getHandle()
    {
        return $this->handle;
    }

}
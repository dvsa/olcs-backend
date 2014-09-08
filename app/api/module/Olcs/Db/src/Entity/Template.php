<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Olcs\Db\Entity\Traits;

/**
 * Template Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="template")
 */
class Template implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity;

    /**
     * History
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Olcs\Db\Entity\History", mappedBy="templates", fetch="LAZY")
     */
    protected $historys;

    /**
     * Data
     *
     * @var string
     *
     * @ORM\Column(type="text", name="data", length=65535, nullable=true)
     */
    protected $data;

    /**
     * Initialise the collections
     */
    public function __construct()
    {
        $this->historys = new ArrayCollection();
    }

    /**
     * Set the history
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $historys
     * @return Template
     */
    public function setHistorys($historys)
    {
        $this->historys = $historys;

        return $this;
    }

    /**
     * Get the historys
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getHistorys()
    {
        return $this->historys;
    }


    /**
     * Add a historys
     * This method exists to make doctrine hydrator happy, it is not currently in use anywhere in the app and probably
     * doesn't work, if needed it should be changed to use doctrine colelction add/remove directly inside a loop as this
     * will save database calls when updating an entity
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $historys
     * @return Template
     */
    public function addHistorys($historys)
    {
        if ($historys instanceof ArrayCollection) {
            $this->historys = new ArrayCollection(
                array_merge(
                    $this->historys->toArray(),
                    $historys->toArray()
                )
            );
        } elseif (!$this->historys->contains($historys)) {
            $this->historys->add($historys);
        }

        return $this;
    }

    /**
     * Remove a historys
     * This method exists to make doctrine hydrator happy, it is not currently in use anywhere in the app and probably
     * doesn't work, if needed it should be updated to take either an iterable or a single object and to determine if it
     * should use remove or removeElement to remove the object (use is_scalar)
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $historys
     * @return Template
     */
    public function removeHistorys($historys)
    {
        if ($this->historys->contains($historys)) {
            $this->historys->remove($historys);
        }

        return $this;
    }

    /**
     * Set the data
     *
     * @param string $data
     * @return Template
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Get the data
     *
     * @return string
     */
    public function getData()
    {
        return $this->data;
    }

}

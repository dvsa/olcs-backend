<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

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
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $historys
     * @return Template
     */
    public function removeHistorys($historys)
    {
        if ($this->historys->contains($historys)) {
            $this->historys->removeElement($historys);
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

    /**
     * Clear properties
     *
     * @param type $properties
     */
    public function clearProperties($properties = array())
    {
        foreach ($properties as $property) {

            if (property_exists($this, $property)) {
                if ($this->$property instanceof Collection) {

                    $this->$property = new ArrayCollection(array());

                } else {

                    $this->$property = null;
                }
            }
        }
    }

    /**
     * Set the id
     *
     * @param int $id
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
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
}

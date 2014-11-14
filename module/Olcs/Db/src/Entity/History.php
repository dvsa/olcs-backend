<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * History Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="history",
 *    indexes={
 *        @ORM\Index(name="fk_history_entity1_idx", 
 *            columns={"entity_type_id"})
 *    }
 * )
 */
class History implements Interfaces\EntityInterface
{

    /**
     * Entity type
     *
     * @var \Olcs\Db\Entity\EntityType
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\EntityType", fetch="LAZY")
     * @ORM\JoinColumn(name="entity_type_id", referencedColumnName="id", nullable=false)
     */
    protected $entityType;

    /**
     * Template
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Olcs\Db\Entity\Template", inversedBy="historys", fetch="LAZY")
     * @ORM\JoinTable(name="history_template",
     *     joinColumns={
     *         @ORM\JoinColumn(name="history_id", referencedColumnName="id")
     *     },
     *     inverseJoinColumns={
     *         @ORM\JoinColumn(name="template_id", referencedColumnName="id")
     *     }
     * )
     */
    protected $templates;

    /**
     * Data
     *
     * @var string
     *
     * @ORM\Column(type="text", name="data", length=65535, nullable=false)
     */
    protected $data;

    /**
     * Entity id
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="entity_id", nullable=false)
     */
    protected $entityId;

    /**
     * Entity version
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="entity_version", nullable=false)
     */
    protected $entityVersion;

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
        $this->templates = new ArrayCollection();
    }

    /**
     * Set the entity type
     *
     * @param \Olcs\Db\Entity\EntityType $entityType
     * @return History
     */
    public function setEntityType($entityType)
    {
        $this->entityType = $entityType;

        return $this;
    }

    /**
     * Get the entity type
     *
     * @return \Olcs\Db\Entity\EntityType
     */
    public function getEntityType()
    {
        return $this->entityType;
    }

    /**
     * Set the template
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $templates
     * @return History
     */
    public function setTemplates($templates)
    {
        $this->templates = $templates;

        return $this;
    }

    /**
     * Get the templates
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getTemplates()
    {
        return $this->templates;
    }

    /**
     * Add a templates
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $templates
     * @return History
     */
    public function addTemplates($templates)
    {
        if ($templates instanceof ArrayCollection) {
            $this->templates = new ArrayCollection(
                array_merge(
                    $this->templates->toArray(),
                    $templates->toArray()
                )
            );
        } elseif (!$this->templates->contains($templates)) {
            $this->templates->add($templates);
        }

        return $this;
    }

    /**
     * Remove a templates
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $templates
     * @return History
     */
    public function removeTemplates($templates)
    {
        if ($this->templates->contains($templates)) {
            $this->templates->removeElement($templates);
        }

        return $this;
    }

    /**
     * Set the data
     *
     * @param string $data
     * @return History
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
     * Set the entity id
     *
     * @param int $entityId
     * @return History
     */
    public function setEntityId($entityId)
    {
        $this->entityId = $entityId;

        return $this;
    }

    /**
     * Get the entity id
     *
     * @return int
     */
    public function getEntityId()
    {
        return $this->entityId;
    }

    /**
     * Set the entity version
     *
     * @param int $entityVersion
     * @return History
     */
    public function setEntityVersion($entityVersion)
    {
        $this->entityVersion = $entityVersion;

        return $this;
    }

    /**
     * Get the entity version
     *
     * @return int
     */
    public function getEntityVersion()
    {
        return $this->entityVersion;
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

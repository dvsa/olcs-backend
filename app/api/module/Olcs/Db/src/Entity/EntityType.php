<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EntityType Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="entity_type",
 *    indexes={
 *        @ORM\Index(name="fk_entity_type_template1_idx", 
 *            columns={"current_template_id"})
 *    }
 * )
 */
class EntityType implements Interfaces\EntityInterface
{

    /**
     * Current template
     *
     * @var \Olcs\Db\Entity\Template
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Template", fetch="LAZY")
     * @ORM\JoinColumn(name="current_template_id", referencedColumnName="id", nullable=false)
     */
    protected $currentTemplate;

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
     * @ORM\Column(type="string", name="name", length=45, nullable=false)
     */
    protected $name;

    /**
     * Set the current template
     *
     * @param \Olcs\Db\Entity\Template $currentTemplate
     * @return EntityType
     */
    public function setCurrentTemplate($currentTemplate)
    {
        $this->currentTemplate = $currentTemplate;

        return $this;
    }

    /**
     * Get the current template
     *
     * @return \Olcs\Db\Entity\Template
     */
    public function getCurrentTemplate()
    {
        return $this->currentTemplate;
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

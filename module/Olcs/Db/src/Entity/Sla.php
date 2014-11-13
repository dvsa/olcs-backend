<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Sla Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="sla")
 */
class Sla implements Interfaces\EntityInterface
{

    /**
     * Category
     *
     * @var string
     *
     * @ORM\Column(type="string", name="category", length=32, nullable=true)
     */
    protected $category = '';

    /**
     * Field
     *
     * @var string
     *
     * @ORM\Column(type="string", name="field", length=32, nullable=true)
     */
    protected $field = '';

    /**
     * Compare to
     *
     * @var string
     *
     * @ORM\Column(type="string", name="compare_to", length=32, nullable=true)
     */
    protected $compareTo;

    /**
     * Days
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="days", nullable=true)
     */
    protected $days;

    /**
     * Effective from
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="effective_from", nullable=true)
     */
    protected $effectiveFrom;

    /**
     * Effective to
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="effective_to", nullable=true)
     */
    protected $effectiveTo;

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
     * Set the category
     *
     * @param string $category
     * @return Sla
     */
    public function setCategory($category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get the category
     *
     * @return string
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set the field
     *
     * @param string $field
     * @return Sla
     */
    public function setField($field)
    {
        $this->field = $field;

        return $this;
    }

    /**
     * Get the field
     *
     * @return string
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * Set the compare to
     *
     * @param string $compareTo
     * @return Sla
     */
    public function setCompareTo($compareTo)
    {
        $this->compareTo = $compareTo;

        return $this;
    }

    /**
     * Get the compare to
     *
     * @return string
     */
    public function getCompareTo()
    {
        return $this->compareTo;
    }

    /**
     * Set the days
     *
     * @param int $days
     * @return Sla
     */
    public function setDays($days)
    {
        $this->days = $days;

        return $this;
    }

    /**
     * Get the days
     *
     * @return int
     */
    public function getDays()
    {
        return $this->days;
    }

    /**
     * Set the effective from
     *
     * @param \DateTime $effectiveFrom
     * @return Sla
     */
    public function setEffectiveFrom($effectiveFrom)
    {
        $this->effectiveFrom = $effectiveFrom;

        return $this;
    }

    /**
     * Get the effective from
     *
     * @return \DateTime
     */
    public function getEffectiveFrom()
    {
        return $this->effectiveFrom;
    }

    /**
     * Set the effective to
     *
     * @param \DateTime $effectiveTo
     * @return Sla
     */
    public function setEffectiveTo($effectiveTo)
    {
        $this->effectiveTo = $effectiveTo;

        return $this;
    }

    /**
     * Get the effective to
     *
     * @return \DateTime
     */
    public function getEffectiveTo()
    {
        return $this->effectiveTo;
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

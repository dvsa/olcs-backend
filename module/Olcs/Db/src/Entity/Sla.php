<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

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
    use Traits\CustomBaseEntity,
        Traits\IdIdentity;

    /**
     * Category
     *
     * @var string
     *
     * @ORM\Column(type="string", name="category", length=32, nullable=true)
     */
    protected $category;

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
     * Field
     *
     * @var string
     *
     * @ORM\Column(type="string", name="field", length=32, nullable=true)
     */
    protected $field;

    /**
     * Public holiday
     *
     * @var boolean
     *
     * @ORM\Column(type="boolean", name="public_holiday", nullable=false, options={"default": 0})
     */
    protected $publicHoliday = 0;

    /**
     * Weekend
     *
     * @var boolean
     *
     * @ORM\Column(type="boolean", name="weekend", nullable=false, options={"default": 0})
     */
    protected $weekend = 0;

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
     * Set the public holiday
     *
     * @param boolean $publicHoliday
     * @return Sla
     */
    public function setPublicHoliday($publicHoliday)
    {
        $this->publicHoliday = $publicHoliday;

        return $this;
    }

    /**
     * Get the public holiday
     *
     * @return boolean
     */
    public function getPublicHoliday()
    {
        return $this->publicHoliday;
    }

    /**
     * Set the weekend
     *
     * @param boolean $weekend
     * @return Sla
     */
    public function setWeekend($weekend)
    {
        $this->weekend = $weekend;

        return $this;
    }

    /**
     * Get the weekend
     *
     * @return boolean
     */
    public function getWeekend()
    {
        return $this->weekend;
    }
}

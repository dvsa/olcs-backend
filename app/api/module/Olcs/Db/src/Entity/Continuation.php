<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * Continuation Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="continuation",
 *    indexes={
 *        @ORM\Index(name="ix_continuation_type", columns={"type"}),
 *        @ORM\Index(name="ix_continuation_month", columns={"month"}),
 *        @ORM\Index(name="ix_continuation_year", columns={"year"}),
 *        @ORM\Index(name="ix_continuation_traffic_area_id", columns={"traffic_area_id"}),
 *        @ORM\Index(name="ix_continuation_created_by", columns={"created_by"})
 *    }
 * )
 */
class Continuation implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\CreatedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\IdIdentity,
        Traits\TrafficAreaManyToOneAlt1,
        Traits\CustomVersionField;

    /**
     * Month
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="month", nullable=false)
     */
    protected $month;

    /**
     * Type
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData")
     * @ORM\JoinColumn(name="type", referencedColumnName="id", nullable=true)
     */
    protected $type;

    /**
     * Year
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="year", nullable=false)
     */
    protected $year;

    /**
     * Set the month
     *
     * @param int $month
     * @return Continuation
     */
    public function setMonth($month)
    {
        $this->month = $month;

        return $this;
    }

    /**
     * Get the month
     *
     * @return int
     */
    public function getMonth()
    {
        return $this->month;
    }

    /**
     * Set the type
     *
     * @param \Olcs\Db\Entity\RefData $type
     * @return Continuation
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get the type
     *
     * @return \Olcs\Db\Entity\RefData
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set the year
     *
     * @param int $year
     * @return Continuation
     */
    public function setYear($year)
    {
        $this->year = $year;

        return $this;
    }

    /**
     * Get the year
     *
     * @return int
     */
    public function getYear()
    {
        return $this->year;
    }
}

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

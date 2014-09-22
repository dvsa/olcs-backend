<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * PiHearing Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="pi_hearing",
 *    indexes={
 *        @ORM\Index(name="fk_pi_reschedule_dates_pi_detail1_idx", columns={"pi_id"}),
 *        @ORM\Index(name="fk_pi_reschedule_dates_presiding_tc1_idx", columns={"presiding_tc_id"}),
 *        @ORM\Index(name="fk_pi_reschedule_date_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_pi_reschedule_date_user2_idx", columns={"last_modified_by"}),
 *        @ORM\Index(name="fk_pi_hearing_ref_data1_idx", columns={"presided_by_role"})
 *    }
 * )
 */
class PiHearing implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CreatedByManyToOne,
        Traits\PresidingTcManyToOne,
        Traits\HearingDateField,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Presided by role
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData", fetch="LAZY")
     * @ORM\JoinColumn(name="presided_by_role", referencedColumnName="id", nullable=true)
     */
    protected $presidedByRole;

    /**
     * Pi
     *
     * @var \Olcs\Db\Entity\Pi
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Pi", fetch="LAZY", inversedBy="piHearings")
     * @ORM\JoinColumn(name="pi_id", referencedColumnName="id", nullable=false)
     */
    protected $pi;

    /**
     * Presiding tc other
     *
     * @var string
     *
     * @ORM\Column(type="string", name="presiding_tc_other", length=45, nullable=true)
     */
    protected $presidingTcOther;

    /**
     * Venue
     *
     * @var string
     *
     * @ORM\Column(type="string", name="venue", length=255, nullable=true)
     */
    protected $venue;

    /**
     * Set the presided by role
     *
     * @param \Olcs\Db\Entity\RefData $presidedByRole
     * @return PiHearing
     */
    public function setPresidedByRole($presidedByRole)
    {
        $this->presidedByRole = $presidedByRole;

        return $this;
    }

    /**
     * Get the presided by role
     *
     * @return \Olcs\Db\Entity\RefData
     */
    public function getPresidedByRole()
    {
        return $this->presidedByRole;
    }

    /**
     * Set the pi
     *
     * @param \Olcs\Db\Entity\Pi $pi
     * @return PiHearing
     */
    public function setPi($pi)
    {
        $this->pi = $pi;

        return $this;
    }

    /**
     * Get the pi
     *
     * @return \Olcs\Db\Entity\Pi
     */
    public function getPi()
    {
        return $this->pi;
    }

    /**
     * Set the presiding tc other
     *
     * @param string $presidingTcOther
     * @return PiHearing
     */
    public function setPresidingTcOther($presidingTcOther)
    {
        $this->presidingTcOther = $presidingTcOther;

        return $this;
    }

    /**
     * Get the presiding tc other
     *
     * @return string
     */
    public function getPresidingTcOther()
    {
        return $this->presidingTcOther;
    }

    /**
     * Set the venue
     *
     * @param string $venue
     * @return PiHearing
     */
    public function setVenue($venue)
    {
        $this->venue = $venue;

        return $this;
    }

    /**
     * Get the venue
     *
     * @return string
     */
    public function getVenue()
    {
        return $this->venue;
    }
}

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
 *        @ORM\Index(name="IDX_83AFD387E0DEB379", columns={"pi_id"}),
 *        @ORM\Index(name="IDX_83AFD387DE12AB56", columns={"created_by"}),
 *        @ORM\Index(name="IDX_83AFD38765CF370E", columns={"last_modified_by"}),
 *        @ORM\Index(name="IDX_83AFD38753BAD7A2", columns={"presiding_tc_id"})
 *    }
 * )
 */
class PiHearing implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\PiManyToOne,
        Traits\CreatedByManyToOne,
        Traits\LastModifiedByManyToOne,
        Traits\PresidingTcManyToOne,
        Traits\HearingDateField,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Is adjourned
     *
     * @var unknown
     *
     * @ORM\Column(type="yesno", name="is_adjourned", nullable=false)
     */
    protected $isAdjourned;

    /**
     * Venue
     *
     * @var string
     *
     * @ORM\Column(type="string", name="venue", length=100, nullable=true)
     */
    protected $venue;


    /**
     * Set the is adjourned
     *
     * @param unknown $isAdjourned
     * @return PiHearing
     */
    public function setIsAdjourned($isAdjourned)
    {
        $this->isAdjourned = $isAdjourned;

        return $this;
    }

    /**
     * Get the is adjourned
     *
     * @return unknown
     */
    public function getIsAdjourned()
    {
        return $this->isAdjourned;
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

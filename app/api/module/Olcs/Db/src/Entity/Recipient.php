<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Olcs\Db\Entity\Traits;

/**
 * Recipient Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="recipient",
 *    indexes={
 *        @ORM\Index(name="fk_recipient_contact_details1_idx", columns={"contact_details_id"}),
 *        @ORM\Index(name="fk_recipient_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_recipient_user2_idx", columns={"last_modified_by"})
 *    }
 * )
 */
class Recipient implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CreatedByManyToOne,
        Traits\ContactDetailsManyToOne,
        Traits\DeletedDateField,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Traffic area
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Olcs\Db\Entity\TrafficArea", inversedBy="recipients")
     * @ORM\JoinTable(name="recipient_traffic_area",
     *     joinColumns={
     *         @ORM\JoinColumn(name="recipient_id", referencedColumnName="id")
     *     },
     *     inverseJoinColumns={
     *         @ORM\JoinColumn(name="traffic_area_id", referencedColumnName="id")
     *     }
     * )
     */
    protected $trafficAreas;

    /**
     * Send app decision
     *
     * @var boolean
     *
     * @ORM\Column(type="yesnonull", name="send_app_decision", nullable=false)
     */
    protected $sendAppDecision = 0;

    /**
     * Send notices procs
     *
     * @var boolean
     *
     * @ORM\Column(type="yesnonull", name="send_notices_procs", nullable=false)
     */
    protected $sendNoticesProcs = 0;

    /**
     * Is police
     *
     * @var boolean
     *
     * @ORM\Column(type="yesnonull", name="is_police", nullable=false)
     */
    protected $isPolice = 0;

    /**
     * Is objector
     *
     * @var boolean
     *
     * @ORM\Column(type="yesnonull", name="is_objector", nullable=false)
     */
    protected $isObjector = 0;

    /**
     * Initialise the collections
     */
    public function __construct()
    {
        $this->trafficAreas = new ArrayCollection();
    }

    /**
     * Set the traffic area
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $trafficAreas

     * @return \Olcs\Db\Entity\Recipient
     */
    public function setTrafficAreas($trafficAreas)
    {
        $this->trafficAreas = $trafficAreas;

        return $this;
    }

    /**
     * Get the traffic area
     *
     * @return \Doctrine\Common\Collections\ArrayCollection

     */
    public function getTrafficAreas()
    {
        return $this->trafficAreas;
    }

    /**
     * Set the send app decision
     *
     * @param boolean $sendAppDecision
     * @return \Olcs\Db\Entity\Recipient
     */
    public function setSendAppDecision($sendAppDecision)
    {
        $this->sendAppDecision = $sendAppDecision;

        return $this;
    }

    /**
     * Get the send app decision
     *
     * @return boolean
     */
    public function getSendAppDecision()
    {
        return $this->sendAppDecision;
    }

    /**
     * Set the send notices procs
     *
     * @param boolean $sendNoticesProcs
     * @return \Olcs\Db\Entity\Recipient
     */
    public function setSendNoticesProcs($sendNoticesProcs)
    {
        $this->sendNoticesProcs = $sendNoticesProcs;

        return $this;
    }

    /**
     * Get the send notices procs
     *
     * @return boolean
     */
    public function getSendNoticesProcs()
    {
        return $this->sendNoticesProcs;
    }

    /**
     * Set the is police
     *
     * @param boolean $isPolice
     * @return \Olcs\Db\Entity\Recipient
     */
    public function setIsPolice($isPolice)
    {
        $this->isPolice = $isPolice;

        return $this;
    }

    /**
     * Get the is police
     *
     * @return boolean
     */
    public function getIsPolice()
    {
        return $this->isPolice;
    }

    /**
     * Set the is objector
     *
     * @param boolean $isObjector
     * @return \Olcs\Db\Entity\Recipient
     */
    public function setIsObjector($isObjector)
    {
        $this->isObjector = $isObjector;

        return $this;
    }

    /**
     * Get the is objector
     *
     * @return boolean
     */
    public function getIsObjector()
    {
        return $this->isObjector;
    }
}

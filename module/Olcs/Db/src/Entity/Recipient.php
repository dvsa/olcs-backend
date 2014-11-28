<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Olcs\Db\Entity\Traits;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Recipient Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @Gedmo\SoftDeleteable(fieldName="deletedDate", timeAware=true)
 * @ORM\Table(name="recipient",
 *    indexes={
 *        @ORM\Index(name="fk_recipient_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_recipient_user2_idx", columns={"last_modified_by"})
 *    }
 * )
 */
class Recipient implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\CreatedByManyToOne,
        Traits\LastModifiedByManyToOne,
        Traits\CustomDeletedDateField,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Traffic area
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Olcs\Db\Entity\TrafficArea", inversedBy="recipients", fetch="LAZY")
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
     * @var string
     *
     * @ORM\Column(type="yesno", name="send_app_decision", nullable=false)
     */
    protected $sendAppDecision = 0;

    /**
     * Send notices procs
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="send_notices_procs", nullable=false)
     */
    protected $sendNoticesProcs = 0;

    /**
     * Is police
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="is_police", nullable=false)
     */
    protected $isPolice = 0;

    /**
     * Is objector
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="is_objector", nullable=false)
     */
    protected $isObjector = 0;

    /**
     * Contact name
     *
     * @var string
     *
     * @ORM\Column(type="string", name="contact_name", length=100, nullable=true)
     */
    protected $contactName;

    /**
     * Email address
     *
     * @var string
     *
     * @ORM\Column(type="string", name="email_address", length=100, nullable=true)
     */
    protected $emailAddress;

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
     * @return Recipient
     */
    public function setTrafficAreas($trafficAreas)
    {
        $this->trafficAreas = $trafficAreas;

        return $this;
    }

    /**
     * Get the traffic areas
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getTrafficAreas()
    {
        return $this->trafficAreas;
    }

    /**
     * Add a traffic areas
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $trafficAreas
     * @return Recipient
     */
    public function addTrafficAreas($trafficAreas)
    {
        if ($trafficAreas instanceof ArrayCollection) {
            $this->trafficAreas = new ArrayCollection(
                array_merge(
                    $this->trafficAreas->toArray(),
                    $trafficAreas->toArray()
                )
            );
        } elseif (!$this->trafficAreas->contains($trafficAreas)) {
            $this->trafficAreas->add($trafficAreas);
        }

        return $this;
    }

    /**
     * Remove a traffic areas
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $trafficAreas
     * @return Recipient
     */
    public function removeTrafficAreas($trafficAreas)
    {
        if ($this->trafficAreas->contains($trafficAreas)) {
            $this->trafficAreas->removeElement($trafficAreas);
        }

        return $this;
    }

    /**
     * Set the send app decision
     *
     * @param string $sendAppDecision
     * @return Recipient
     */
    public function setSendAppDecision($sendAppDecision)
    {
        $this->sendAppDecision = $sendAppDecision;

        return $this;
    }

    /**
     * Get the send app decision
     *
     * @return string
     */
    public function getSendAppDecision()
    {
        return $this->sendAppDecision;
    }

    /**
     * Set the send notices procs
     *
     * @param string $sendNoticesProcs
     * @return Recipient
     */
    public function setSendNoticesProcs($sendNoticesProcs)
    {
        $this->sendNoticesProcs = $sendNoticesProcs;

        return $this;
    }

    /**
     * Get the send notices procs
     *
     * @return string
     */
    public function getSendNoticesProcs()
    {
        return $this->sendNoticesProcs;
    }

    /**
     * Set the is police
     *
     * @param string $isPolice
     * @return Recipient
     */
    public function setIsPolice($isPolice)
    {
        $this->isPolice = $isPolice;

        return $this;
    }

    /**
     * Get the is police
     *
     * @return string
     */
    public function getIsPolice()
    {
        return $this->isPolice;
    }

    /**
     * Set the is objector
     *
     * @param string $isObjector
     * @return Recipient
     */
    public function setIsObjector($isObjector)
    {
        $this->isObjector = $isObjector;

        return $this;
    }

    /**
     * Get the is objector
     *
     * @return string
     */
    public function getIsObjector()
    {
        return $this->isObjector;
    }

    /**
     * Set the contact name
     *
     * @param string $contactName
     * @return Recipient
     */
    public function setContactName($contactName)
    {
        $this->contactName = $contactName;

        return $this;
    }

    /**
     * Get the contact name
     *
     * @return string
     */
    public function getContactName()
    {
        return $this->contactName;
    }

    /**
     * Set the email address
     *
     * @param string $emailAddress
     * @return Recipient
     */
    public function setEmailAddress($emailAddress)
    {
        $this->emailAddress = $emailAddress;

        return $this;
    }

    /**
     * Get the email address
     *
     * @return string
     */
    public function getEmailAddress()
    {
        return $this->emailAddress;
    }
}

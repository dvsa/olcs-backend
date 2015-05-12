<?php

namespace Dvsa\Olcs\Api\Entity\Publication;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Recipient Abstract Entity
 *
 * Auto-Generated
 *
 * @ORM\HasLifecycleCallbacks
 * @Gedmo\SoftDeleteable(fieldName="deletedDate", timeAware=true)
 * @ORM\Table(name="recipient",
 *    indexes={
 *        @ORM\Index(name="ix_recipient_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_recipient_last_modified_by", columns={"last_modified_by"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="uk_recipient_olbs_key", columns={"olbs_key"})
 *    }
 * )
 */
abstract class AbstractRecipient
{

    /**
     * Contact name
     *
     * @var string
     *
     * @ORM\Column(type="string", name="contact_name", length=100, nullable=true)
     */
    protected $contactName;

    /**
     * Created by
     *
     * @var \Dvsa\Olcs\Api\Entity\User\User
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\User\User")
     * @ORM\JoinColumn(name="created_by", referencedColumnName="id", nullable=true)
     */
    protected $createdBy;

    /**
     * Created on
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="created_on", nullable=true)
     */
    protected $createdOn;

    /**
     * Deleted date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="deleted_date", nullable=true)
     */
    protected $deletedDate;

    /**
     * Email address
     *
     * @var string
     *
     * @ORM\Column(type="string", name="email_address", length=100, nullable=true)
     */
    protected $emailAddress;

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
     * Is objector
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="is_objector", nullable=false, options={"default": 0})
     */
    protected $isObjector = 0;

    /**
     * Is police
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="is_police", nullable=false, options={"default": 0})
     */
    protected $isPolice = 0;

    /**
     * Last modified by
     *
     * @var \Dvsa\Olcs\Api\Entity\User\User
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\User\User")
     * @ORM\JoinColumn(name="last_modified_by", referencedColumnName="id", nullable=true)
     */
    protected $lastModifiedBy;

    /**
     * Last modified on
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="last_modified_on", nullable=true)
     */
    protected $lastModifiedOn;

    /**
     * Olbs key
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="olbs_key", nullable=true)
     */
    protected $olbsKey;

    /**
     * Send app decision
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="send_app_decision", nullable=false, options={"default": 0})
     */
    protected $sendAppDecision = 0;

    /**
     * Send notices procs
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="send_notices_procs", nullable=false, options={"default": 0})
     */
    protected $sendNoticesProcs = 0;

    /**
     * Traffic area
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Dvsa\Olcs\Api\Entity\TrafficArea\TrafficArea", inversedBy="recipients")
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
     * Version
     *
     * @var int
     *
     * @ORM\Version
     * @ORM\Column(type="smallint", name="version", nullable=false, options={"default": 1})
     */
    protected $version = 1;

    /**
     * Initialise the collections
     */
    public function __construct()
    {
        $this->trafficAreas = new ArrayCollection();
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
     * Set the created by
     *
     * @param \Dvsa\Olcs\Api\Entity\User\User $createdBy
     * @return Recipient
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get the created by
     *
     * @return \Dvsa\Olcs\Api\Entity\User\User
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Set the created on
     *
     * @param \DateTime $createdOn
     * @return Recipient
     */
    public function setCreatedOn($createdOn)
    {
        $this->createdOn = $createdOn;

        return $this;
    }

    /**
     * Get the created on
     *
     * @return \DateTime
     */
    public function getCreatedOn()
    {
        return $this->createdOn;
    }

    /**
     * Set the deleted date
     *
     * @param \DateTime $deletedDate
     * @return Recipient
     */
    public function setDeletedDate($deletedDate)
    {
        $this->deletedDate = $deletedDate;

        return $this;
    }

    /**
     * Get the deleted date
     *
     * @return \DateTime
     */
    public function getDeletedDate()
    {
        return $this->deletedDate;
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

    /**
     * Set the id
     *
     * @param int $id
     * @return Recipient
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
     * Set the last modified by
     *
     * @param \Dvsa\Olcs\Api\Entity\User\User $lastModifiedBy
     * @return Recipient
     */
    public function setLastModifiedBy($lastModifiedBy)
    {
        $this->lastModifiedBy = $lastModifiedBy;

        return $this;
    }

    /**
     * Get the last modified by
     *
     * @return \Dvsa\Olcs\Api\Entity\User\User
     */
    public function getLastModifiedBy()
    {
        return $this->lastModifiedBy;
    }

    /**
     * Set the last modified on
     *
     * @param \DateTime $lastModifiedOn
     * @return Recipient
     */
    public function setLastModifiedOn($lastModifiedOn)
    {
        $this->lastModifiedOn = $lastModifiedOn;

        return $this;
    }

    /**
     * Get the last modified on
     *
     * @return \DateTime
     */
    public function getLastModifiedOn()
    {
        return $this->lastModifiedOn;
    }

    /**
     * Set the olbs key
     *
     * @param int $olbsKey
     * @return Recipient
     */
    public function setOlbsKey($olbsKey)
    {
        $this->olbsKey = $olbsKey;

        return $this;
    }

    /**
     * Get the olbs key
     *
     * @return int
     */
    public function getOlbsKey()
    {
        return $this->olbsKey;
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
     * Set the version
     *
     * @param int $version
     * @return Recipient
     */
    public function setVersion($version)
    {
        $this->version = $version;

        return $this;
    }

    /**
     * Get the version
     *
     * @return int
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Set the createdOn field on persist
     *
     * @ORM\PrePersist
     */
    public function setCreatedOnBeforePersist()
    {
        $this->createdOn = new \DateTime();
    }

    /**
     * Set the lastModifiedOn field on persist
     *
     * @ORM\PreUpdate
     */
    public function setLastModifiedOnBeforeUpdate()
    {
        $this->lastModifiedOn = new \DateTime();
    }
}

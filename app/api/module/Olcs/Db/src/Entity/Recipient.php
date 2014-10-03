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
 *        @ORM\Index(name="IDX_6804FB49DE12AB56", columns={"created_by"}),
 *        @ORM\Index(name="IDX_6804FB497CA35EB5", columns={"contact_details_id"}),
 *        @ORM\Index(name="IDX_6804FB4965CF370E", columns={"last_modified_by"})
 *    }
 * )
 */
class Recipient implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\CreatedByManyToOne,
        Traits\ContactDetailsManyToOne,
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
    protected $sendAppDecision;

    /**
     * Send notices procs
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="send_notices_procs", nullable=false)
     */
    protected $sendNoticesProcs;

    /**
     * Is police
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="is_police", nullable=false)
     */
    protected $isPolice;

    /**
     * Is objector
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="is_objector", nullable=false)
     */
    protected $isObjector;

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
     * This method exists to make doctrine hydrator happy, it is not currently in use anywhere in the app and probably
     * doesn't work, if needed it should be changed to use doctrine colelction add/remove directly inside a loop as this
     * will save database calls when updating an entity
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
     * This method exists to make doctrine hydrator happy, it is not currently in use anywhere in the app and probably
     * doesn't work, if needed it should be updated to take either an iterable or a single object and to determine if it
     * should use remove or removeElement to remove the object (use is_scalar)
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
}

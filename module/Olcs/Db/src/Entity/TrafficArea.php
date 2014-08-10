<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Olcs\Db\Entity\Traits;

/**
 * TrafficArea Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="traffic_area",
 *    indexes={
 *        @ORM\Index(name="fk_traffic_area_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_traffic_area_user2_idx", columns={"last_modified_by"}),
 *        @ORM\Index(name="fk_traffic_area_contact_details1_idx", columns={"contact_details_id"})
 *    }
 * )
 */
class TrafficArea implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\ContactDetailsManyToOne,
        Traits\LastModifiedByManyToOne,
        Traits\CreatedByManyToOne,
        Traits\Name70Field,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Identifier - Id
     *
     * @var string
     *
     * @ORM\Id
     * @ORM\Column(type="string", name="id", length=1)
     */
    protected $id;

    /**
     * Recipient
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Olcs\Db\Entity\Recipient", mappedBy="trafficAreas")
     */
    protected $recipients;

    /**
     * Txc name
     *
     * @var string
     *
     * @ORM\Column(type="string", name="txc_name", length=70, nullable=true)
     */
    protected $txcName;

    /**
     * Initialise the collections
     */
    public function __construct()
    {
        $this->recipients = new ArrayCollection();
    }

    /**
     * Get identifier(s)
     *
     * @return mixed
     */
    public function getIdentifier()
    {
        return $this->getId();
    }

    /**
     * Set the id
     *
     * @param string $id
     * @return TrafficArea
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the id
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }


    /**
     * Set the recipient
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $recipients
     * @return TrafficArea
     */
    public function setRecipients($recipients)
    {
        $this->recipients = $recipients;

        return $this;
    }

    /**
     * Get the recipients
     *
     * @return \Doctrine\Common\Collections\ArrayCollection

     */
    public function getRecipients()
    {
        return $this->recipients;
    }

    /**
     * Add a recipients
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $recipients
     * @return TrafficArea
     */
    public function addRecipients($recipients)
    {
        if ($recipients instanceof ArrayCollection) {
            $this->recipients = new ArrayCollection(
                array_merge(
                    $this->recipients->toArray(),
                    $recipients->toArray()
                )
            );
        } elseif (!$this->recipients->contains($recipients)) {
            $this->recipients->add($recipients);
        }

        return $this;
    }

    /**
     * Remove a recipients
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $recipients
     * @return TrafficArea
     */
    public function removeRecipients($recipients)
    {
        if ($this->recipients->contains($recipients)) {
            $this->recipients->remove($recipients);
        }

        return $this;
    }


    /**
     * Set the txc name
     *
     * @param string $txcName
     * @return TrafficArea
     */
    public function setTxcName($txcName)
    {
        $this->txcName = $txcName;

        return $this;
    }

    /**
     * Get the txc name
     *
     * @return string
     */
    public function getTxcName()
    {
        return $this->txcName;
    }

}

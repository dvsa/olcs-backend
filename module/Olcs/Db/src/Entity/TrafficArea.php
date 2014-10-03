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
 *        @ORM\Index(name="IDX_21D26726DE12AB56", columns={"created_by"}),
 *        @ORM\Index(name="IDX_21D267267CA35EB5", columns={"contact_details_id"}),
 *        @ORM\Index(name="IDX_21D2672665CF370E", columns={"last_modified_by"})
 *    }
 * )
 */
class TrafficArea implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\CreatedByManyToOne,
        Traits\ContactDetailsManyToOne,
        Traits\LastModifiedByManyToOne,
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
     * @ORM\ManyToMany(targetEntity="Olcs\Db\Entity\Recipient", mappedBy="trafficAreas", fetch="LAZY")
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
     * Is scottish rules
     *
     * @var boolean
     *
     * @ORM\Column(type="boolean", name="is_scottish_rules", nullable=false)
     */
    protected $isScottishRules;

    /**
     * Document
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Olcs\Db\Entity\Document", mappedBy="trafficArea")
     */
    protected $documents;

    /**
     * Initialise the collections
     */
    public function __construct()
    {
        $this->recipients = new ArrayCollection();
        $this->documents = new ArrayCollection();
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
     * This method exists to make doctrine hydrator happy, it is not currently in use anywhere in the app and probably
     * doesn't work, if needed it should be changed to use doctrine colelction add/remove directly inside a loop as this
     * will save database calls when updating an entity
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
     * This method exists to make doctrine hydrator happy, it is not currently in use anywhere in the app and probably
     * doesn't work, if needed it should be updated to take either an iterable or a single object and to determine if it
     * should use remove or removeElement to remove the object (use is_scalar)
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $recipients
     * @return TrafficArea
     */
    public function removeRecipients($recipients)
    {
        if ($this->recipients->contains($recipients)) {
            $this->recipients->removeElement($recipients);
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

    /**
     * Set the is scottish rules
     *
     * @param boolean $isScottishRules
     * @return TrafficArea
     */
    public function setIsScottishRules($isScottishRules)
    {
        $this->isScottishRules = $isScottishRules;

        return $this;
    }

    /**
     * Get the is scottish rules
     *
     * @return boolean
     */
    public function getIsScottishRules()
    {
        return $this->isScottishRules;
    }

    /**
     * Set the document
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $documents
     * @return TrafficArea
     */
    public function setDocuments($documents)
    {
        $this->documents = $documents;

        return $this;
    }

    /**
     * Get the documents
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getDocuments()
    {
        return $this->documents;
    }

    /**
     * Add a documents
     * This method exists to make doctrine hydrator happy, it is not currently in use anywhere in the app and probably
     * doesn't work, if needed it should be changed to use doctrine colelction add/remove directly inside a loop as this
     * will save database calls when updating an entity
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $documents
     * @return TrafficArea
     */
    public function addDocuments($documents)
    {
        if ($documents instanceof ArrayCollection) {
            $this->documents = new ArrayCollection(
                array_merge(
                    $this->documents->toArray(),
                    $documents->toArray()
                )
            );
        } elseif (!$this->documents->contains($documents)) {
            $this->documents->add($documents);
        }

        return $this;
    }

    /**
     * Remove a documents
     * This method exists to make doctrine hydrator happy, it is not currently in use anywhere in the app and probably
     * doesn't work, if needed it should be updated to take either an iterable or a single object and to determine if it
     * should use remove or removeElement to remove the object (use is_scalar)
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $documents
     * @return TrafficArea
     */
    public function removeDocuments($documents)
    {
        if ($this->documents->contains($documents)) {
            $this->documents->removeElement($documents);
        }

        return $this;
    }
}

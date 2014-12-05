<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Olcs\Db\Entity\Traits;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * TransportManager Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @Gedmo\SoftDeleteable(fieldName="deletedDate", timeAware=true)
 * @ORM\Table(name="transport_manager",
 *    indexes={
 *        @ORM\Index(name="fk_transport_manager_ref_data1_idx", columns={"tm_status"}),
 *        @ORM\Index(name="fk_transport_manager_ref_data2_idx", columns={"tm_type"}),
 *        @ORM\Index(name="fk_transport_manager_contact_details1_idx", columns={"contact_details_id"}),
 *        @ORM\Index(name="fk_transport_manager_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_transport_manager_user2_idx", columns={"last_modified_by"})
 *    }
 * )
 */
class TransportManager implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\ContactDetailsManyToOne,
        Traits\CreatedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\CustomDeletedDateField,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CustomLastModifiedOnField,
        Traits\Notes4000Field,
        Traits\CustomVersionField;

    /**
     * Disqualification tm case id
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="disqualification_tm_case_id", nullable=true)
     */
    protected $disqualificationTmCaseId;

    /**
     * Nysiis family name
     *
     * @var string
     *
     * @ORM\Column(type="string", name="nysiis_family_name", length=100, nullable=true)
     */
    protected $nysiisFamilyName;

    /**
     * Nysiis forename
     *
     * @var string
     *
     * @ORM\Column(type="string", name="nysiis_forename", length=100, nullable=true)
     */
    protected $nysiisForename;

    /**
     * Tm status
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData")
     * @ORM\JoinColumn(name="tm_status", referencedColumnName="id", nullable=false)
     */
    protected $tmStatus;

    /**
     * Tm type
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData")
     * @ORM\JoinColumn(name="tm_type", referencedColumnName="id", nullable=false)
     */
    protected $tmType;

    /**
     * Document
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Olcs\Db\Entity\Document", mappedBy="transportManager")
     */
    protected $documents;

    /**
     * Initialise the collections
     */
    public function __construct()
    {
        $this->documents = new ArrayCollection();
    }

    /**
     * Set the disqualification tm case id
     *
     * @param int $disqualificationTmCaseId
     * @return TransportManager
     */
    public function setDisqualificationTmCaseId($disqualificationTmCaseId)
    {
        $this->disqualificationTmCaseId = $disqualificationTmCaseId;

        return $this;
    }

    /**
     * Get the disqualification tm case id
     *
     * @return int
     */
    public function getDisqualificationTmCaseId()
    {
        return $this->disqualificationTmCaseId;
    }

    /**
     * Set the nysiis family name
     *
     * @param string $nysiisFamilyName
     * @return TransportManager
     */
    public function setNysiisFamilyName($nysiisFamilyName)
    {
        $this->nysiisFamilyName = $nysiisFamilyName;

        return $this;
    }

    /**
     * Get the nysiis family name
     *
     * @return string
     */
    public function getNysiisFamilyName()
    {
        return $this->nysiisFamilyName;
    }

    /**
     * Set the nysiis forename
     *
     * @param string $nysiisForename
     * @return TransportManager
     */
    public function setNysiisForename($nysiisForename)
    {
        $this->nysiisForename = $nysiisForename;

        return $this;
    }

    /**
     * Get the nysiis forename
     *
     * @return string
     */
    public function getNysiisForename()
    {
        return $this->nysiisForename;
    }

    /**
     * Set the tm status
     *
     * @param \Olcs\Db\Entity\RefData $tmStatus
     * @return TransportManager
     */
    public function setTmStatus($tmStatus)
    {
        $this->tmStatus = $tmStatus;

        return $this;
    }

    /**
     * Get the tm status
     *
     * @return \Olcs\Db\Entity\RefData
     */
    public function getTmStatus()
    {
        return $this->tmStatus;
    }

    /**
     * Set the tm type
     *
     * @param \Olcs\Db\Entity\RefData $tmType
     * @return TransportManager
     */
    public function setTmType($tmType)
    {
        $this->tmType = $tmType;

        return $this;
    }

    /**
     * Get the tm type
     *
     * @return \Olcs\Db\Entity\RefData
     */
    public function getTmType()
    {
        return $this->tmType;
    }

    /**
     * Set the document
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $documents
     * @return TransportManager
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
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $documents
     * @return TransportManager
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
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $documents
     * @return TransportManager
     */
    public function removeDocuments($documents)
    {
        if ($this->documents->contains($documents)) {
            $this->documents->removeElement($documents);
        }

        return $this;
    }
}

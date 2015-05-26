<?php

namespace Dvsa\Olcs\Api\Entity\Licence;

use Doctrine\ORM\Mapping as ORM;

/**
 * ContinuationDetail Abstract Entity
 *
 * Auto-Generated
 *
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="continuation_detail",
 *    indexes={
 *        @ORM\Index(name="ix_continuation_detail_continuation_id", columns={"continuation_id"}),
 *        @ORM\Index(name="ix_continuation_detail_licence_id", columns={"licence_id"}),
 *        @ORM\Index(name="ix_continuation_detail_checklist_document_id",
     *     columns={"checklist_document_id"}),
 *        @ORM\Index(name="ix_continuation_detail_status", columns={"status"}),
 *        @ORM\Index(name="ix_continuation_detail_received", columns={"received"}),
 *        @ORM\Index(name="ix_continuation_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_continuation_last_modified_by", columns={"last_modified_by"})
 *    }
 * )
 */
abstract class AbstractContinuationDetail
{

    /**
     * Checklist document
     *
     * @var \Dvsa\Olcs\Api\Entity\Doc\Document
     *
     * @ORM\ManyToOne(
     *     targetEntity="Dvsa\Olcs\Api\Entity\Doc\Document",
     *     fetch="LAZY",
     *     inversedBy="continuationDetails"
     * )
     * @ORM\JoinColumn(name="checklist_document_id", referencedColumnName="id", nullable=true)
     */
    protected $checklistDocument;

    /**
     * Continuation
     *
     * @var \Dvsa\Olcs\Api\Entity\Licence\Continuation
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\Licence\Continuation", fetch="LAZY")
     * @ORM\JoinColumn(name="continuation_id", referencedColumnName="id", nullable=false)
     */
    protected $continuation;

    /**
     * Created by
     *
     * @var \Dvsa\Olcs\Api\Entity\User\User
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\User\User", fetch="LAZY")
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
     * Last modified by
     *
     * @var \Dvsa\Olcs\Api\Entity\User\User
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\User\User", fetch="LAZY")
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
     * Licence
     *
     * @var \Dvsa\Olcs\Api\Entity\Licence\Licence
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\Licence\Licence", fetch="LAZY")
     * @ORM\JoinColumn(name="licence_id", referencedColumnName="id", nullable=false)
     */
    protected $licence;

    /**
     * Received
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="received", nullable=false, options={"default": 0})
     */
    protected $received = 0;

    /**
     * Status
     *
     * @var \Dvsa\Olcs\Api\Entity\System\RefData
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\System\RefData", fetch="LAZY")
     * @ORM\JoinColumn(name="status", referencedColumnName="id", nullable=true)
     */
    protected $status;

    /**
     * Tot auth vehicles
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="tot_auth_vehicles", nullable=true)
     */
    protected $totAuthVehicles;

    /**
     * Tot community licences
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="tot_community_licences", nullable=true)
     */
    protected $totCommunityLicences;

    /**
     * Tot psv discs
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="tot_psv_discs", nullable=true)
     */
    protected $totPsvDiscs;

    /**
     * Version
     *
     * @var int
     *
     * @ORM\Column(type="smallint", name="version", nullable=false, options={"default": 1})
     * @ORM\Version
     */
    protected $version = 1;

    /**
     * Set the checklist document
     *
     * @param \Dvsa\Olcs\Api\Entity\Doc\Document $checklistDocument
     * @return ContinuationDetail
     */
    public function setChecklistDocument($checklistDocument)
    {
        $this->checklistDocument = $checklistDocument;

        return $this;
    }

    /**
     * Get the checklist document
     *
     * @return \Dvsa\Olcs\Api\Entity\Doc\Document
     */
    public function getChecklistDocument()
    {
        return $this->checklistDocument;
    }

    /**
     * Set the continuation
     *
     * @param \Dvsa\Olcs\Api\Entity\Licence\Continuation $continuation
     * @return ContinuationDetail
     */
    public function setContinuation($continuation)
    {
        $this->continuation = $continuation;

        return $this;
    }

    /**
     * Get the continuation
     *
     * @return \Dvsa\Olcs\Api\Entity\Licence\Continuation
     */
    public function getContinuation()
    {
        return $this->continuation;
    }

    /**
     * Set the created by
     *
     * @param \Dvsa\Olcs\Api\Entity\User\User $createdBy
     * @return ContinuationDetail
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
     * @return ContinuationDetail
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
     * Set the id
     *
     * @param int $id
     * @return ContinuationDetail
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
     * Set the last modified by
     *
     * @param \Dvsa\Olcs\Api\Entity\User\User $lastModifiedBy
     * @return ContinuationDetail
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
     * @return ContinuationDetail
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
     * Set the licence
     *
     * @param \Dvsa\Olcs\Api\Entity\Licence\Licence $licence
     * @return ContinuationDetail
     */
    public function setLicence($licence)
    {
        $this->licence = $licence;

        return $this;
    }

    /**
     * Get the licence
     *
     * @return \Dvsa\Olcs\Api\Entity\Licence\Licence
     */
    public function getLicence()
    {
        return $this->licence;
    }

    /**
     * Set the received
     *
     * @param string $received
     * @return ContinuationDetail
     */
    public function setReceived($received)
    {
        $this->received = $received;

        return $this;
    }

    /**
     * Get the received
     *
     * @return string
     */
    public function getReceived()
    {
        return $this->received;
    }

    /**
     * Set the status
     *
     * @param \Dvsa\Olcs\Api\Entity\System\RefData $status
     * @return ContinuationDetail
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get the status
     *
     * @return \Dvsa\Olcs\Api\Entity\System\RefData
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set the tot auth vehicles
     *
     * @param int $totAuthVehicles
     * @return ContinuationDetail
     */
    public function setTotAuthVehicles($totAuthVehicles)
    {
        $this->totAuthVehicles = $totAuthVehicles;

        return $this;
    }

    /**
     * Get the tot auth vehicles
     *
     * @return int
     */
    public function getTotAuthVehicles()
    {
        return $this->totAuthVehicles;
    }

    /**
     * Set the tot community licences
     *
     * @param int $totCommunityLicences
     * @return ContinuationDetail
     */
    public function setTotCommunityLicences($totCommunityLicences)
    {
        $this->totCommunityLicences = $totCommunityLicences;

        return $this;
    }

    /**
     * Get the tot community licences
     *
     * @return int
     */
    public function getTotCommunityLicences()
    {
        return $this->totCommunityLicences;
    }

    /**
     * Set the tot psv discs
     *
     * @param int $totPsvDiscs
     * @return ContinuationDetail
     */
    public function setTotPsvDiscs($totPsvDiscs)
    {
        $this->totPsvDiscs = $totPsvDiscs;

        return $this;
    }

    /**
     * Get the tot psv discs
     *
     * @return int
     */
    public function getTotPsvDiscs()
    {
        return $this->totPsvDiscs;
    }

    /**
     * Set the version
     *
     * @param int $version
     * @return ContinuationDetail
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

    /**
     * Clear properties
     *
     * @param type $properties
     */
    public function clearProperties($properties = array())
    {
        foreach ($properties as $property) {

            if (property_exists($this, $property)) {
                if ($this->$property instanceof Collection) {

                    $this->$property = new ArrayCollection(array());

                } else {

                    $this->$property = null;
                }
            }
        }
    }
}

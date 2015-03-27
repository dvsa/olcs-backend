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
 *        @ORM\Index(name="ix_transport_manager_tm_status", columns={"tm_status"}),
 *        @ORM\Index(name="ix_transport_manager_tm_type", columns={"tm_type"}),
 *        @ORM\Index(name="ix_transport_manager_home_cd_id", columns={"home_cd_id"}),
 *        @ORM\Index(name="ix_transport_manager_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_transport_manager_last_modified_by", columns={"last_modified_by"}),
 *        @ORM\Index(name="ix_transport_manager_work_cd_id", columns={"work_cd_id"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="uk_transport_manager_olbs_key", columns={"olbs_key"})
 *    }
 * )
 */
class TransportManager implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\CreatedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\CustomDeletedDateField,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CustomLastModifiedOnField,
        Traits\Notes4000Field,
        Traits\OlbsKeyField,
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
     * Home cd
     *
     * @var \Olcs\Db\Entity\ContactDetails
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\ContactDetails")
     * @ORM\JoinColumn(name="home_cd_id", referencedColumnName="id", nullable=false)
     */
    protected $homeCd;

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
     * Work cd
     *
     * @var \Olcs\Db\Entity\ContactDetails
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\ContactDetails")
     * @ORM\JoinColumn(name="work_cd_id", referencedColumnName="id", nullable=true)
     */
    protected $workCd;

    /**
     * Document
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Olcs\Db\Entity\Document", mappedBy="transportManager")
     */
    protected $documents;

    /**
     * Other licence
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Olcs\Db\Entity\OtherLicence", mappedBy="transportManager")
     */
    protected $otherLicences;

    /**
     * Previous conviction
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Olcs\Db\Entity\PreviousConviction", mappedBy="transportManager")
     */
    protected $previousConvictions;

    /**
     * Employment
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Olcs\Db\Entity\TmEmployment", mappedBy="transportManager")
     */
    protected $employments;

    /**
     * Qualification
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Olcs\Db\Entity\TmQualification", mappedBy="transportManager")
     */
    protected $qualifications;

    /**
     * Tm application
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Olcs\Db\Entity\TransportManagerApplication", mappedBy="transportManager")
     */
    protected $tmApplications;

    /**
     * Tm licence
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Olcs\Db\Entity\TransportManagerLicence", mappedBy="transportManager")
     */
    protected $tmLicences;

    /**
     * Initialise the collections
     */
    public function __construct()
    {
        $this->documents = new ArrayCollection();
        $this->otherLicences = new ArrayCollection();
        $this->previousConvictions = new ArrayCollection();
        $this->employments = new ArrayCollection();
        $this->qualifications = new ArrayCollection();
        $this->tmApplications = new ArrayCollection();
        $this->tmLicences = new ArrayCollection();
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
     * Set the home cd
     *
     * @param \Olcs\Db\Entity\ContactDetails $homeCd
     * @return TransportManager
     */
    public function setHomeCd($homeCd)
    {
        $this->homeCd = $homeCd;

        return $this;
    }

    /**
     * Get the home cd
     *
     * @return \Olcs\Db\Entity\ContactDetails
     */
    public function getHomeCd()
    {
        return $this->homeCd;
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
     * Set the work cd
     *
     * @param \Olcs\Db\Entity\ContactDetails $workCd
     * @return TransportManager
     */
    public function setWorkCd($workCd)
    {
        $this->workCd = $workCd;

        return $this;
    }

    /**
     * Get the work cd
     *
     * @return \Olcs\Db\Entity\ContactDetails
     */
    public function getWorkCd()
    {
        return $this->workCd;
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

    /**
     * Set the other licence
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $otherLicences
     * @return TransportManager
     */
    public function setOtherLicences($otherLicences)
    {
        $this->otherLicences = $otherLicences;

        return $this;
    }

    /**
     * Get the other licences
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getOtherLicences()
    {
        return $this->otherLicences;
    }

    /**
     * Add a other licences
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $otherLicences
     * @return TransportManager
     */
    public function addOtherLicences($otherLicences)
    {
        if ($otherLicences instanceof ArrayCollection) {
            $this->otherLicences = new ArrayCollection(
                array_merge(
                    $this->otherLicences->toArray(),
                    $otherLicences->toArray()
                )
            );
        } elseif (!$this->otherLicences->contains($otherLicences)) {
            $this->otherLicences->add($otherLicences);
        }

        return $this;
    }

    /**
     * Remove a other licences
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $otherLicences
     * @return TransportManager
     */
    public function removeOtherLicences($otherLicences)
    {
        if ($this->otherLicences->contains($otherLicences)) {
            $this->otherLicences->removeElement($otherLicences);
        }

        return $this;
    }

    /**
     * Set the previous conviction
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $previousConvictions
     * @return TransportManager
     */
    public function setPreviousConvictions($previousConvictions)
    {
        $this->previousConvictions = $previousConvictions;

        return $this;
    }

    /**
     * Get the previous convictions
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getPreviousConvictions()
    {
        return $this->previousConvictions;
    }

    /**
     * Add a previous convictions
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $previousConvictions
     * @return TransportManager
     */
    public function addPreviousConvictions($previousConvictions)
    {
        if ($previousConvictions instanceof ArrayCollection) {
            $this->previousConvictions = new ArrayCollection(
                array_merge(
                    $this->previousConvictions->toArray(),
                    $previousConvictions->toArray()
                )
            );
        } elseif (!$this->previousConvictions->contains($previousConvictions)) {
            $this->previousConvictions->add($previousConvictions);
        }

        return $this;
    }

    /**
     * Remove a previous convictions
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $previousConvictions
     * @return TransportManager
     */
    public function removePreviousConvictions($previousConvictions)
    {
        if ($this->previousConvictions->contains($previousConvictions)) {
            $this->previousConvictions->removeElement($previousConvictions);
        }

        return $this;
    }

    /**
     * Set the employment
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $employments
     * @return TransportManager
     */
    public function setEmployments($employments)
    {
        $this->employments = $employments;

        return $this;
    }

    /**
     * Get the employments
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getEmployments()
    {
        return $this->employments;
    }

    /**
     * Add a employments
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $employments
     * @return TransportManager
     */
    public function addEmployments($employments)
    {
        if ($employments instanceof ArrayCollection) {
            $this->employments = new ArrayCollection(
                array_merge(
                    $this->employments->toArray(),
                    $employments->toArray()
                )
            );
        } elseif (!$this->employments->contains($employments)) {
            $this->employments->add($employments);
        }

        return $this;
    }

    /**
     * Remove a employments
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $employments
     * @return TransportManager
     */
    public function removeEmployments($employments)
    {
        if ($this->employments->contains($employments)) {
            $this->employments->removeElement($employments);
        }

        return $this;
    }

    /**
     * Set the qualification
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $qualifications
     * @return TransportManager
     */
    public function setQualifications($qualifications)
    {
        $this->qualifications = $qualifications;

        return $this;
    }

    /**
     * Get the qualifications
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getQualifications()
    {
        return $this->qualifications;
    }

    /**
     * Add a qualifications
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $qualifications
     * @return TransportManager
     */
    public function addQualifications($qualifications)
    {
        if ($qualifications instanceof ArrayCollection) {
            $this->qualifications = new ArrayCollection(
                array_merge(
                    $this->qualifications->toArray(),
                    $qualifications->toArray()
                )
            );
        } elseif (!$this->qualifications->contains($qualifications)) {
            $this->qualifications->add($qualifications);
        }

        return $this;
    }

    /**
     * Remove a qualifications
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $qualifications
     * @return TransportManager
     */
    public function removeQualifications($qualifications)
    {
        if ($this->qualifications->contains($qualifications)) {
            $this->qualifications->removeElement($qualifications);
        }

        return $this;
    }

    /**
     * Set the tm application
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $tmApplications
     * @return TransportManager
     */
    public function setTmApplications($tmApplications)
    {
        $this->tmApplications = $tmApplications;

        return $this;
    }

    /**
     * Get the tm applications
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getTmApplications()
    {
        return $this->tmApplications;
    }

    /**
     * Add a tm applications
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $tmApplications
     * @return TransportManager
     */
    public function addTmApplications($tmApplications)
    {
        if ($tmApplications instanceof ArrayCollection) {
            $this->tmApplications = new ArrayCollection(
                array_merge(
                    $this->tmApplications->toArray(),
                    $tmApplications->toArray()
                )
            );
        } elseif (!$this->tmApplications->contains($tmApplications)) {
            $this->tmApplications->add($tmApplications);
        }

        return $this;
    }

    /**
     * Remove a tm applications
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $tmApplications
     * @return TransportManager
     */
    public function removeTmApplications($tmApplications)
    {
        if ($this->tmApplications->contains($tmApplications)) {
            $this->tmApplications->removeElement($tmApplications);
        }

        return $this;
    }

    /**
     * Set the tm licence
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $tmLicences
     * @return TransportManager
     */
    public function setTmLicences($tmLicences)
    {
        $this->tmLicences = $tmLicences;

        return $this;
    }

    /**
     * Get the tm licences
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getTmLicences()
    {
        return $this->tmLicences;
    }

    /**
     * Add a tm licences
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $tmLicences
     * @return TransportManager
     */
    public function addTmLicences($tmLicences)
    {
        if ($tmLicences instanceof ArrayCollection) {
            $this->tmLicences = new ArrayCollection(
                array_merge(
                    $this->tmLicences->toArray(),
                    $tmLicences->toArray()
                )
            );
        } elseif (!$this->tmLicences->contains($tmLicences)) {
            $this->tmLicences->add($tmLicences);
        }

        return $this;
    }

    /**
     * Remove a tm licences
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $tmLicences
     * @return TransportManager
     */
    public function removeTmLicences($tmLicences)
    {
        if ($this->tmLicences->contains($tmLicences)) {
            $this->tmLicences->removeElement($tmLicences);
        }

        return $this;
    }
}

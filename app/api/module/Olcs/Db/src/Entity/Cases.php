<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Olcs\Db\Entity\Traits;

/**
 * Cases Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="cases",
 *    indexes={
 *        @ORM\Index(name="fk_case_application1_idx", columns={"application_id"}),
 *        @ORM\Index(name="fk_case_licence1_idx", columns={"licence_id"}),
 *        @ORM\Index(name="fk_case_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_case_user2_idx", columns={"last_modified_by"}),
 *        @ORM\Index(name="fk_cases_transport_manager1_idx", columns={"transport_manager_id"}),
 *        @ORM\Index(name="fk_cases_ref_data1_idx", columns={"case_type"})
 *    }
 * )
 */
class Cases implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\TransportManagerManyToOne,
        Traits\ApplicationManyToOne,
        Traits\LastModifiedByManyToOne,
        Traits\CreatedByManyToOne,
        Traits\LicenceManyToOne,
        Traits\DeletedDateField,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Case type
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData")
     * @ORM\JoinColumn(name="case_type", referencedColumnName="id")
     */
    protected $caseType;

    /**
     * Legacy offence
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Olcs\Db\Entity\LegacyOffence", inversedBy="cases")
     * @ORM\JoinTable(name="legacy_case_offence",
     *     joinColumns={
     *         @ORM\JoinColumn(name="case_id", referencedColumnName="id")
     *     },
     *     inverseJoinColumns={
     *         @ORM\JoinColumn(name="legacy_offence_id", referencedColumnName="id")
     *     }
     * )
     */
    protected $legacyOffences;

    /**
     * Submission section
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Olcs\Db\Entity\SubmissionSection", inversedBy="cases")
     * @ORM\JoinTable(name="case_submission_section",
     *     joinColumns={
     *         @ORM\JoinColumn(name="case_id", referencedColumnName="id")
     *     },
     *     inverseJoinColumns={
     *         @ORM\JoinColumn(name="submission_section_id", referencedColumnName="id")
     *     }
     * )
     */
    protected $submissionSections;

    /**
     * Ecms no
     *
     * @var string
     *
     * @ORM\Column(type="string", name="ecms_no", length=45, nullable=true)
     */
    protected $ecmsNo;

    /**
     * Open date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="open_date", nullable=true)
     */
    protected $openDate;

    /**
     * Close date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="close_date", nullable=true)
     */
    protected $closeDate;

    /**
     * Description
     *
     * @var string
     *
     * @ORM\Column(type="string", name="description", length=1024, nullable=true)
     */
    protected $description;

    /**
     * Is impounding
     *
     * @var boolean
     *
     * @ORM\Column(type="yesnonull", name="is_impounding", nullable=false)
     */
    protected $isImpounding = 0;

    /**
     * Erru originating authority
     *
     * @var string
     *
     * @ORM\Column(type="string", name="erru_originating_authority", length=50, nullable=true)
     */
    protected $erruOriginatingAuthority;

    /**
     * Erru transport undertaking name
     *
     * @var string
     *
     * @ORM\Column(type="string", name="erru_transport_undertaking_name", length=100, nullable=true)
     */
    protected $erruTransportUndertakingName;

    /**
     * Erru vrm
     *
     * @var string
     *
     * @ORM\Column(type="string", name="erru_vrm", length=15, nullable=true)
     */
    protected $erruVrm;

    /**
     * Annual test history
     *
     * @var string
     *
     * @ORM\Column(type="string", name="annual_test_history", length=4000, nullable=true)
     */
    protected $annualTestHistory;

    /**
     * Initialise the collections
     */
    public function __construct()
    {
        $this->legacyOffences = new ArrayCollection();
        $this->submissionSections = new ArrayCollection();
    }

    /**
     * Set the case type
     *
     * @param \Olcs\Db\Entity\RefData $caseType
     * @return \Olcs\Db\Entity\Cases
     */
    public function setCaseType($caseType)
    {
        $this->caseType = $caseType;

        return $this;
    }

    /**
     * Get the case type
     *
     * @return \Olcs\Db\Entity\RefData
     */
    public function getCaseType()
    {
        return $this->caseType;
    }

    /**
     * Set the legacy offence
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $legacyOffences

     * @return \Olcs\Db\Entity\Cases
     */
    public function setLegacyOffences($legacyOffences)
    {
        $this->legacyOffences = $legacyOffences;

        return $this;
    }

    /**
     * Get the legacy offence
     *
     * @return \Doctrine\Common\Collections\ArrayCollection

     */
    public function getLegacyOffences()
    {
        return $this->legacyOffences;
    }

    /**
     * Set the submission section
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $submissionSections

     * @return \Olcs\Db\Entity\Cases
     */
    public function setSubmissionSections($submissionSections)
    {
        $this->submissionSections = $submissionSections;

        return $this;
    }

    /**
     * Get the submission section
     *
     * @return \Doctrine\Common\Collections\ArrayCollection

     */
    public function getSubmissionSections()
    {
        return $this->submissionSections;
    }

    /**
     * Set the ecms no
     *
     * @param string $ecmsNo
     * @return \Olcs\Db\Entity\Cases
     */
    public function setEcmsNo($ecmsNo)
    {
        $this->ecmsNo = $ecmsNo;

        return $this;
    }

    /**
     * Get the ecms no
     *
     * @return string
     */
    public function getEcmsNo()
    {
        return $this->ecmsNo;
    }

    /**
     * Set the open date
     *
     * @param \DateTime $openDate
     * @return \Olcs\Db\Entity\Cases
     */
    public function setOpenDate($openDate)
    {
        $this->openDate = $openDate;

        return $this;
    }

    /**
     * Get the open date
     *
     * @return \DateTime
     */
    public function getOpenDate()
    {
        return $this->openDate;
    }

    /**
     * Set the close date
     *
     * @param \DateTime $closeDate
     * @return \Olcs\Db\Entity\Cases
     */
    public function setCloseDate($closeDate)
    {
        $this->closeDate = $closeDate;

        return $this;
    }

    /**
     * Get the close date
     *
     * @return \DateTime
     */
    public function getCloseDate()
    {
        return $this->closeDate;
    }

    /**
     * Set the description
     *
     * @param string $description
     * @return \Olcs\Db\Entity\Cases
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get the description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set the is impounding
     *
     * @param boolean $isImpounding
     * @return \Olcs\Db\Entity\Cases
     */
    public function setIsImpounding($isImpounding)
    {
        $this->isImpounding = $isImpounding;

        return $this;
    }

    /**
     * Get the is impounding
     *
     * @return boolean
     */
    public function getIsImpounding()
    {
        return $this->isImpounding;
    }

    /**
     * Set the erru originating authority
     *
     * @param string $erruOriginatingAuthority
     * @return \Olcs\Db\Entity\Cases
     */
    public function setErruOriginatingAuthority($erruOriginatingAuthority)
    {
        $this->erruOriginatingAuthority = $erruOriginatingAuthority;

        return $this;
    }

    /**
     * Get the erru originating authority
     *
     * @return string
     */
    public function getErruOriginatingAuthority()
    {
        return $this->erruOriginatingAuthority;
    }

    /**
     * Set the erru transport undertaking name
     *
     * @param string $erruTransportUndertakingName
     * @return \Olcs\Db\Entity\Cases
     */
    public function setErruTransportUndertakingName($erruTransportUndertakingName)
    {
        $this->erruTransportUndertakingName = $erruTransportUndertakingName;

        return $this;
    }

    /**
     * Get the erru transport undertaking name
     *
     * @return string
     */
    public function getErruTransportUndertakingName()
    {
        return $this->erruTransportUndertakingName;
    }

    /**
     * Set the erru vrm
     *
     * @param string $erruVrm
     * @return \Olcs\Db\Entity\Cases
     */
    public function setErruVrm($erruVrm)
    {
        $this->erruVrm = $erruVrm;

        return $this;
    }

    /**
     * Get the erru vrm
     *
     * @return string
     */
    public function getErruVrm()
    {
        return $this->erruVrm;
    }

    /**
     * Set the annual test history
     *
     * @param string $annualTestHistory
     * @return \Olcs\Db\Entity\Cases
     */
    public function setAnnualTestHistory($annualTestHistory)
    {
        $this->annualTestHistory = $annualTestHistory;

        return $this;
    }

    /**
     * Get the annual test history
     *
     * @return string
     */
    public function getAnnualTestHistory()
    {
        return $this->annualTestHistory;
    }
}

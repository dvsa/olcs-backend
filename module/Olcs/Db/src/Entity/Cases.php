<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Olcs\Db\Entity\Traits;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Cases Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @Gedmo\SoftDeleteable(fieldName="deletedDate", timeAware=true)
 * @ORM\Table(name="cases",
 *    indexes={
 *        @ORM\Index(name="fk_case_application1_idx", columns={"application_id"}),
 *        @ORM\Index(name="fk_case_licence1_idx", columns={"licence_id"}),
 *        @ORM\Index(name="fk_case_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_case_user2_idx", columns={"last_modified_by"}),
 *        @ORM\Index(name="fk_cases_transport_manager1_idx", columns={"transport_manager_id"}),
 *        @ORM\Index(name="fk_cases_ref_data1_idx", columns={"case_type"}),
 *        @ORM\Index(name="fk_cases_ref_data2_idx", columns={"erru_case_type"})
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
        Traits\CloseDateField,
        Traits\CustomDeletedDateField,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Erru case type
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData")
     * @ORM\JoinColumn(name="erru_case_type", referencedColumnName="id", nullable=true)
     */
    protected $erruCaseType;

    /**
     * Case type
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData")
     * @ORM\JoinColumn(name="case_type", referencedColumnName="id", nullable=false)
     */
    protected $caseType;

    /**
     * Licence
     *
     * @var \Olcs\Db\Entity\Licence
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Licence", inversedBy="cases")
     * @ORM\JoinColumn(name="licence_id", referencedColumnName="id", nullable=true)
     */
    protected $licence;

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
     * Category
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Olcs\Db\Entity\RefData", inversedBy="cases")
     * @ORM\JoinTable(name="case_category",
     *     joinColumns={
     *         @ORM\JoinColumn(name="case_id", referencedColumnName="id")
     *     },
     *     inverseJoinColumns={
     *         @ORM\JoinColumn(name="category_id", referencedColumnName="id")
     *     }
     * )
     */
    protected $categorys;

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
     * @ORM\Column(type="datetime", name="open_date", nullable=false)
     */
    protected $openDate;

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
     * @var string
     *
     * @ORM\Column(type="yesno", name="is_impounding", nullable=false)
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
     * Prohibition note
     *
     * @var string
     *
     * @ORM\Column(type="string", name="prohibition_note", length=4000, nullable=true)
     */
    protected $prohibitionNote;

    /**
     * Penalties note
     *
     * @var string
     *
     * @ORM\Column(type="string", name="penalties_note", length=4000, nullable=true)
     */
    protected $penaltiesNote;

    /**
     * Conviction note
     *
     * @var string
     *
     * @ORM\Column(type="string", name="conviction_note", length=4000, nullable=true)
     */
    protected $convictionNote;

    /**
     * Appeal
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Olcs\Db\Entity\Appeal", mappedBy="case")
     */
    protected $appeals;

    /**
     * Condition undertaking
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Olcs\Db\Entity\ConditionUndertaking", mappedBy="case")
     */
    protected $conditionUndertakings;

    /**
     * Conviction
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Olcs\Db\Entity\Conviction", mappedBy="case")
     */
    protected $convictions;

    /**
     * Document
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Olcs\Db\Entity\Document", mappedBy="case")
     */
    protected $documents;

    /**
     * Serious infringement
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Olcs\Db\Entity\SeriousInfringement", mappedBy="case")
     */
    protected $seriousInfringements;

    /**
     * Stay
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Olcs\Db\Entity\Stay", mappedBy="case")
     */
    protected $stays;

    /**
     * Initialise the collections
     */
    public function __construct()
    {
        $this->legacyOffences = new ArrayCollection();
        $this->categorys = new ArrayCollection();
        $this->appeals = new ArrayCollection();
        $this->conditionUndertakings = new ArrayCollection();
        $this->convictions = new ArrayCollection();
        $this->documents = new ArrayCollection();
        $this->seriousInfringements = new ArrayCollection();
        $this->stays = new ArrayCollection();
    }

    /**
     * Set the erru case type
     *
     * @param \Olcs\Db\Entity\RefData $erruCaseType
     * @return Cases
     */
    public function setErruCaseType($erruCaseType)
    {
        $this->erruCaseType = $erruCaseType;

        return $this;
    }

    /**
     * Get the erru case type
     *
     * @return \Olcs\Db\Entity\RefData
     */
    public function getErruCaseType()
    {
        return $this->erruCaseType;
    }

    /**
     * Set the case type
     *
     * @param \Olcs\Db\Entity\RefData $caseType
     * @return Cases
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
     * Set the licence
     *
     * @param \Olcs\Db\Entity\Licence $licence
     * @return Cases
     */
    public function setLicence($licence)
    {
        $this->licence = $licence;

        return $this;
    }

    /**
     * Get the licence
     *
     * @return \Olcs\Db\Entity\Licence
     */
    public function getLicence()
    {
        return $this->licence;
    }

    /**
     * Set the legacy offence
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $legacyOffences
     * @return Cases
     */
    public function setLegacyOffences($legacyOffences)
    {
        $this->legacyOffences = $legacyOffences;

        return $this;
    }

    /**
     * Get the legacy offences
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getLegacyOffences()
    {
        return $this->legacyOffences;
    }

    /**
     * Add a legacy offences
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $legacyOffences
     * @return Cases
     */
    public function addLegacyOffences($legacyOffences)
    {
        if ($legacyOffences instanceof ArrayCollection) {
            $this->legacyOffences = new ArrayCollection(
                array_merge(
                    $this->legacyOffences->toArray(),
                    $legacyOffences->toArray()
                )
            );
        } elseif (!$this->legacyOffences->contains($legacyOffences)) {
            $this->legacyOffences->add($legacyOffences);
        }

        return $this;
    }

    /**
     * Remove a legacy offences
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $legacyOffences
     * @return Cases
     */
    public function removeLegacyOffences($legacyOffences)
    {
        if ($this->legacyOffences->contains($legacyOffences)) {
            $this->legacyOffences->removeElement($legacyOffences);
        }

        return $this;
    }

    /**
     * Set the category
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $categorys
     * @return Cases
     */
    public function setCategorys($categorys)
    {
        $this->categorys = $categorys;

        return $this;
    }

    /**
     * Get the categorys
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getCategorys()
    {
        return $this->categorys;
    }

    /**
     * Add a categorys
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $categorys
     * @return Cases
     */
    public function addCategorys($categorys)
    {
        if ($categorys instanceof ArrayCollection) {
            $this->categorys = new ArrayCollection(
                array_merge(
                    $this->categorys->toArray(),
                    $categorys->toArray()
                )
            );
        } elseif (!$this->categorys->contains($categorys)) {
            $this->categorys->add($categorys);
        }

        return $this;
    }

    /**
     * Remove a categorys
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $categorys
     * @return Cases
     */
    public function removeCategorys($categorys)
    {
        if ($this->categorys->contains($categorys)) {
            $this->categorys->removeElement($categorys);
        }

        return $this;
    }

    /**
     * Set the ecms no
     *
     * @param string $ecmsNo
     * @return Cases
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
     * @return Cases
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
     * Set the description
     *
     * @param string $description
     * @return Cases
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
     * @param string $isImpounding
     * @return Cases
     */
    public function setIsImpounding($isImpounding)
    {
        $this->isImpounding = $isImpounding;

        return $this;
    }

    /**
     * Get the is impounding
     *
     * @return string
     */
    public function getIsImpounding()
    {
        return $this->isImpounding;
    }

    /**
     * Set the erru originating authority
     *
     * @param string $erruOriginatingAuthority
     * @return Cases
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
     * @return Cases
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
     * @return Cases
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
     * @return Cases
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

    /**
     * Set the prohibition note
     *
     * @param string $prohibitionNote
     * @return Cases
     */
    public function setProhibitionNote($prohibitionNote)
    {
        $this->prohibitionNote = $prohibitionNote;

        return $this;
    }

    /**
     * Get the prohibition note
     *
     * @return string
     */
    public function getProhibitionNote()
    {
        return $this->prohibitionNote;
    }

    /**
     * Set the penalties note
     *
     * @param string $penaltiesNote
     * @return Cases
     */
    public function setPenaltiesNote($penaltiesNote)
    {
        $this->penaltiesNote = $penaltiesNote;

        return $this;
    }

    /**
     * Get the penalties note
     *
     * @return string
     */
    public function getPenaltiesNote()
    {
        return $this->penaltiesNote;
    }

    /**
     * Set the conviction note
     *
     * @param string $convictionNote
     * @return Cases
     */
    public function setConvictionNote($convictionNote)
    {
        $this->convictionNote = $convictionNote;

        return $this;
    }

    /**
     * Get the conviction note
     *
     * @return string
     */
    public function getConvictionNote()
    {
        return $this->convictionNote;
    }

    /**
     * Set the appeal
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $appeals
     * @return Cases
     */
    public function setAppeals($appeals)
    {
        $this->appeals = $appeals;

        return $this;
    }

    /**
     * Get the appeals
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getAppeals()
    {
        return $this->appeals;
    }

    /**
     * Add a appeals
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $appeals
     * @return Cases
     */
    public function addAppeals($appeals)
    {
        if ($appeals instanceof ArrayCollection) {
            $this->appeals = new ArrayCollection(
                array_merge(
                    $this->appeals->toArray(),
                    $appeals->toArray()
                )
            );
        } elseif (!$this->appeals->contains($appeals)) {
            $this->appeals->add($appeals);
        }

        return $this;
    }

    /**
     * Remove a appeals
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $appeals
     * @return Cases
     */
    public function removeAppeals($appeals)
    {
        if ($this->appeals->contains($appeals)) {
            $this->appeals->removeElement($appeals);
        }

        return $this;
    }

    /**
     * Set the condition undertaking
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $conditionUndertakings
     * @return Cases
     */
    public function setConditionUndertakings($conditionUndertakings)
    {
        $this->conditionUndertakings = $conditionUndertakings;

        return $this;
    }

    /**
     * Get the condition undertakings
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getConditionUndertakings()
    {
        return $this->conditionUndertakings;
    }

    /**
     * Add a condition undertakings
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $conditionUndertakings
     * @return Cases
     */
    public function addConditionUndertakings($conditionUndertakings)
    {
        if ($conditionUndertakings instanceof ArrayCollection) {
            $this->conditionUndertakings = new ArrayCollection(
                array_merge(
                    $this->conditionUndertakings->toArray(),
                    $conditionUndertakings->toArray()
                )
            );
        } elseif (!$this->conditionUndertakings->contains($conditionUndertakings)) {
            $this->conditionUndertakings->add($conditionUndertakings);
        }

        return $this;
    }

    /**
     * Remove a condition undertakings
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $conditionUndertakings
     * @return Cases
     */
    public function removeConditionUndertakings($conditionUndertakings)
    {
        if ($this->conditionUndertakings->contains($conditionUndertakings)) {
            $this->conditionUndertakings->removeElement($conditionUndertakings);
        }

        return $this;
    }

    /**
     * Set the conviction
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $convictions
     * @return Cases
     */
    public function setConvictions($convictions)
    {
        $this->convictions = $convictions;

        return $this;
    }

    /**
     * Get the convictions
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getConvictions()
    {
        return $this->convictions;
    }

    /**
     * Add a convictions
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $convictions
     * @return Cases
     */
    public function addConvictions($convictions)
    {
        if ($convictions instanceof ArrayCollection) {
            $this->convictions = new ArrayCollection(
                array_merge(
                    $this->convictions->toArray(),
                    $convictions->toArray()
                )
            );
        } elseif (!$this->convictions->contains($convictions)) {
            $this->convictions->add($convictions);
        }

        return $this;
    }

    /**
     * Remove a convictions
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $convictions
     * @return Cases
     */
    public function removeConvictions($convictions)
    {
        if ($this->convictions->contains($convictions)) {
            $this->convictions->removeElement($convictions);
        }

        return $this;
    }

    /**
     * Set the document
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $documents
     * @return Cases
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
     * @return Cases
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
     * @return Cases
     */
    public function removeDocuments($documents)
    {
        if ($this->documents->contains($documents)) {
            $this->documents->removeElement($documents);
        }

        return $this;
    }

    /**
     * Set the serious infringement
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $seriousInfringements
     * @return Cases
     */
    public function setSeriousInfringements($seriousInfringements)
    {
        $this->seriousInfringements = $seriousInfringements;

        return $this;
    }

    /**
     * Get the serious infringements
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getSeriousInfringements()
    {
        return $this->seriousInfringements;
    }

    /**
     * Add a serious infringements
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $seriousInfringements
     * @return Cases
     */
    public function addSeriousInfringements($seriousInfringements)
    {
        if ($seriousInfringements instanceof ArrayCollection) {
            $this->seriousInfringements = new ArrayCollection(
                array_merge(
                    $this->seriousInfringements->toArray(),
                    $seriousInfringements->toArray()
                )
            );
        } elseif (!$this->seriousInfringements->contains($seriousInfringements)) {
            $this->seriousInfringements->add($seriousInfringements);
        }

        return $this;
    }

    /**
     * Remove a serious infringements
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $seriousInfringements
     * @return Cases
     */
    public function removeSeriousInfringements($seriousInfringements)
    {
        if ($this->seriousInfringements->contains($seriousInfringements)) {
            $this->seriousInfringements->removeElement($seriousInfringements);
        }

        return $this;
    }

    /**
     * Set the stay
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $stays
     * @return Cases
     */
    public function setStays($stays)
    {
        $this->stays = $stays;

        return $this;
    }

    /**
     * Get the stays
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getStays()
    {
        return $this->stays;
    }

    /**
     * Add a stays
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $stays
     * @return Cases
     */
    public function addStays($stays)
    {
        if ($stays instanceof ArrayCollection) {
            $this->stays = new ArrayCollection(
                array_merge(
                    $this->stays->toArray(),
                    $stays->toArray()
                )
            );
        } elseif (!$this->stays->contains($stays)) {
            $this->stays->add($stays);
        }

        return $this;
    }

    /**
     * Remove a stays
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $stays
     * @return Cases
     */
    public function removeStays($stays)
    {
        if ($this->stays->contains($stays)) {
            $this->stays->removeElement($stays);
        }

        return $this;
    }
}

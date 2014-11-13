<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Conviction Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="conviction",
 *    indexes={
 *        @ORM\Index(name="fk_conviction_conviction_category1_idx", 
 *            columns={"conviction_category"}),
 *        @ORM\Index(name="fk_conviction_person1_idx", 
 *            columns={"person_id"}),
 *        @ORM\Index(name="fk_conviction_organisation1_idx", 
 *            columns={"organisation_id"}),
 *        @ORM\Index(name="fk_conviction_transport_manager1_idx", 
 *            columns={"transport_manager_id"}),
 *        @ORM\Index(name="fk_conviction_user1_idx", 
 *            columns={"created_by"}),
 *        @ORM\Index(name="fk_conviction_user2_idx", 
 *            columns={"last_modified_by"}),
 *        @ORM\Index(name="fk_conviction_operator_case1_idx", 
 *            columns={"case_id"}),
 *        @ORM\Index(name="fk_conviction_ref_data1_idx", 
 *            columns={"defendant_type"})
 *    }
 * )
 */
class Conviction implements Interfaces\EntityInterface
{

    /**
     * Defendant type
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData", fetch="LAZY")
     * @ORM\JoinColumn(name="defendant_type", referencedColumnName="id", nullable=false)
     */
    protected $defendantType;

    /**
     * Conviction category
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData", fetch="LAZY")
     * @ORM\JoinColumn(name="conviction_category", referencedColumnName="id", nullable=true)
     */
    protected $convictionCategory;

    /**
     * Case
     *
     * @var \Olcs\Db\Entity\Cases
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Cases", fetch="LAZY", inversedBy="convictions")
     * @ORM\JoinColumn(name="case_id", referencedColumnName="id", nullable=false)
     */
    protected $case;

    /**
     * Offence date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="offence_date", nullable=true)
     */
    protected $offenceDate;

    /**
     * Conviction date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="conviction_date", nullable=true)
     */
    protected $convictionDate;

    /**
     * Court
     *
     * @var string
     *
     * @ORM\Column(type="string", name="court", length=70, nullable=true)
     */
    protected $court;

    /**
     * Costs
     *
     * @var string
     *
     * @ORM\Column(type="string", name="costs", length=255, nullable=true)
     */
    protected $costs;

    /**
     * Msi
     *
     * @var string
     *
     * @ORM\Column(type="yesnonull", name="msi", nullable=true)
     */
    protected $msi;

    /**
     * Is dealt with
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="is_dealt_with", nullable=false)
     */
    protected $isDealtWith = 0;

    /**
     * Is declared
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="is_declared", nullable=false)
     */
    protected $isDeclared = 0;

    /**
     * Operator name
     *
     * @var string
     *
     * @ORM\Column(type="string", name="operator_name", length=70, nullable=true)
     */
    protected $operatorName;

    /**
     * Taken into consideration
     *
     * @var string
     *
     * @ORM\Column(type="string", name="taken_into_consideration", length=4000, nullable=true)
     */
    protected $takenIntoConsideration;

    /**
     * Category text
     *
     * @var string
     *
     * @ORM\Column(type="string", name="category_text", length=1024, nullable=true)
     */
    protected $categoryText;

    /**
     * Person firstname
     *
     * @var string
     *
     * @ORM\Column(type="string", name="person_firstname", length=70, nullable=true)
     */
    protected $personFirstname;

    /**
     * Person lastname
     *
     * @var string
     *
     * @ORM\Column(type="string", name="person_lastname", length=70, nullable=true)
     */
    protected $personLastname;

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
     * Transport manager
     *
     * @var \Olcs\Db\Entity\TransportManager
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\TransportManager", fetch="LAZY")
     * @ORM\JoinColumn(name="transport_manager_id", referencedColumnName="id", nullable=false)
     */
    protected $transportManager;

    /**
     * Created by
     *
     * @var \Olcs\Db\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\User", fetch="LAZY")
     * @ORM\JoinColumn(name="created_by", referencedColumnName="id", nullable=true)
     */
    protected $createdBy;

    /**
     * Last modified by
     *
     * @var \Olcs\Db\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\User", fetch="LAZY")
     * @ORM\JoinColumn(name="last_modified_by", referencedColumnName="id", nullable=true)
     */
    protected $lastModifiedBy;

    /**
     * Person
     *
     * @var \Olcs\Db\Entity\Person
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Person", fetch="LAZY")
     * @ORM\JoinColumn(name="person_id", referencedColumnName="id", nullable=true)
     */
    protected $person;

    /**
     * Organisation
     *
     * @var \Olcs\Db\Entity\Organisation
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Organisation", fetch="LAZY")
     * @ORM\JoinColumn(name="organisation_id", referencedColumnName="id", nullable=false)
     */
    protected $organisation;

    /**
     * Penalty
     *
     * @var string
     *
     * @ORM\Column(type="string", name="penalty", length=255, nullable=true)
     */
    protected $penalty;

    /**
     * Birth date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="birth_date", nullable=true)
     */
    protected $birthDate;

    /**
     * Notes
     *
     * @var string
     *
     * @ORM\Column(type="string", name="notes", length=4000, nullable=true)
     */
    protected $notes;

    /**
     * Created on
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="created_on", nullable=true)
     */
    protected $createdOn;

    /**
     * Last modified on
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="last_modified_on", nullable=true)
     */
    protected $lastModifiedOn;

    /**
     * Version
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="version", nullable=false)
     * @ORM\Version
     */
    protected $version;

    /**
     * Set the defendant type
     *
     * @param \Olcs\Db\Entity\RefData $defendantType
     * @return Conviction
     */
    public function setDefendantType($defendantType)
    {
        $this->defendantType = $defendantType;

        return $this;
    }

    /**
     * Get the defendant type
     *
     * @return \Olcs\Db\Entity\RefData
     */
    public function getDefendantType()
    {
        return $this->defendantType;
    }

    /**
     * Set the conviction category
     *
     * @param \Olcs\Db\Entity\RefData $convictionCategory
     * @return Conviction
     */
    public function setConvictionCategory($convictionCategory)
    {
        $this->convictionCategory = $convictionCategory;

        return $this;
    }

    /**
     * Get the conviction category
     *
     * @return \Olcs\Db\Entity\RefData
     */
    public function getConvictionCategory()
    {
        return $this->convictionCategory;
    }

    /**
     * Set the case
     *
     * @param \Olcs\Db\Entity\Cases $case
     * @return Conviction
     */
    public function setCase($case)
    {
        $this->case = $case;

        return $this;
    }

    /**
     * Get the case
     *
     * @return \Olcs\Db\Entity\Cases
     */
    public function getCase()
    {
        return $this->case;
    }

    /**
     * Set the offence date
     *
     * @param \DateTime $offenceDate
     * @return Conviction
     */
    public function setOffenceDate($offenceDate)
    {
        $this->offenceDate = $offenceDate;

        return $this;
    }

    /**
     * Get the offence date
     *
     * @return \DateTime
     */
    public function getOffenceDate()
    {
        return $this->offenceDate;
    }

    /**
     * Set the conviction date
     *
     * @param \DateTime $convictionDate
     * @return Conviction
     */
    public function setConvictionDate($convictionDate)
    {
        $this->convictionDate = $convictionDate;

        return $this;
    }

    /**
     * Get the conviction date
     *
     * @return \DateTime
     */
    public function getConvictionDate()
    {
        return $this->convictionDate;
    }

    /**
     * Set the court
     *
     * @param string $court
     * @return Conviction
     */
    public function setCourt($court)
    {
        $this->court = $court;

        return $this;
    }

    /**
     * Get the court
     *
     * @return string
     */
    public function getCourt()
    {
        return $this->court;
    }

    /**
     * Set the costs
     *
     * @param string $costs
     * @return Conviction
     */
    public function setCosts($costs)
    {
        $this->costs = $costs;

        return $this;
    }

    /**
     * Get the costs
     *
     * @return string
     */
    public function getCosts()
    {
        return $this->costs;
    }

    /**
     * Set the msi
     *
     * @param string $msi
     * @return Conviction
     */
    public function setMsi($msi)
    {
        $this->msi = $msi;

        return $this;
    }

    /**
     * Get the msi
     *
     * @return string
     */
    public function getMsi()
    {
        return $this->msi;
    }

    /**
     * Set the is dealt with
     *
     * @param string $isDealtWith
     * @return Conviction
     */
    public function setIsDealtWith($isDealtWith)
    {
        $this->isDealtWith = $isDealtWith;

        return $this;
    }

    /**
     * Get the is dealt with
     *
     * @return string
     */
    public function getIsDealtWith()
    {
        return $this->isDealtWith;
    }

    /**
     * Set the is declared
     *
     * @param string $isDeclared
     * @return Conviction
     */
    public function setIsDeclared($isDeclared)
    {
        $this->isDeclared = $isDeclared;

        return $this;
    }

    /**
     * Get the is declared
     *
     * @return string
     */
    public function getIsDeclared()
    {
        return $this->isDeclared;
    }

    /**
     * Set the operator name
     *
     * @param string $operatorName
     * @return Conviction
     */
    public function setOperatorName($operatorName)
    {
        $this->operatorName = $operatorName;

        return $this;
    }

    /**
     * Get the operator name
     *
     * @return string
     */
    public function getOperatorName()
    {
        return $this->operatorName;
    }

    /**
     * Set the taken into consideration
     *
     * @param string $takenIntoConsideration
     * @return Conviction
     */
    public function setTakenIntoConsideration($takenIntoConsideration)
    {
        $this->takenIntoConsideration = $takenIntoConsideration;

        return $this;
    }

    /**
     * Get the taken into consideration
     *
     * @return string
     */
    public function getTakenIntoConsideration()
    {
        return $this->takenIntoConsideration;
    }

    /**
     * Set the category text
     *
     * @param string $categoryText
     * @return Conviction
     */
    public function setCategoryText($categoryText)
    {
        $this->categoryText = $categoryText;

        return $this;
    }

    /**
     * Get the category text
     *
     * @return string
     */
    public function getCategoryText()
    {
        return $this->categoryText;
    }

    /**
     * Set the person firstname
     *
     * @param string $personFirstname
     * @return Conviction
     */
    public function setPersonFirstname($personFirstname)
    {
        $this->personFirstname = $personFirstname;

        return $this;
    }

    /**
     * Get the person firstname
     *
     * @return string
     */
    public function getPersonFirstname()
    {
        return $this->personFirstname;
    }

    /**
     * Set the person lastname
     *
     * @param string $personLastname
     * @return Conviction
     */
    public function setPersonLastname($personLastname)
    {
        $this->personLastname = $personLastname;

        return $this;
    }

    /**
     * Get the person lastname
     *
     * @return string
     */
    public function getPersonLastname()
    {
        return $this->personLastname;
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

    /**
     * Set the id
     *
     * @param int $id
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
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
     * Set the transport manager
     *
     * @param \Olcs\Db\Entity\TransportManager $transportManager
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setTransportManager($transportManager)
    {
        $this->transportManager = $transportManager;

        return $this;
    }

    /**
     * Get the transport manager
     *
     * @return \Olcs\Db\Entity\TransportManager
     */
    public function getTransportManager()
    {
        return $this->transportManager;
    }

    /**
     * Set the created by
     *
     * @param \Olcs\Db\Entity\User $createdBy
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get the created by
     *
     * @return \Olcs\Db\Entity\User
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Set the last modified by
     *
     * @param \Olcs\Db\Entity\User $lastModifiedBy
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setLastModifiedBy($lastModifiedBy)
    {
        $this->lastModifiedBy = $lastModifiedBy;

        return $this;
    }

    /**
     * Get the last modified by
     *
     * @return \Olcs\Db\Entity\User
     */
    public function getLastModifiedBy()
    {
        return $this->lastModifiedBy;
    }

    /**
     * Set the person
     *
     * @param \Olcs\Db\Entity\Person $person
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setPerson($person)
    {
        $this->person = $person;

        return $this;
    }

    /**
     * Get the person
     *
     * @return \Olcs\Db\Entity\Person
     */
    public function getPerson()
    {
        return $this->person;
    }

    /**
     * Set the organisation
     *
     * @param \Olcs\Db\Entity\Organisation $organisation
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setOrganisation($organisation)
    {
        $this->organisation = $organisation;

        return $this;
    }

    /**
     * Get the organisation
     *
     * @return \Olcs\Db\Entity\Organisation
     */
    public function getOrganisation()
    {
        return $this->organisation;
    }

    /**
     * Set the penalty
     *
     * @param string $penalty
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setPenalty($penalty)
    {
        $this->penalty = $penalty;

        return $this;
    }

    /**
     * Get the penalty
     *
     * @return string
     */
    public function getPenalty()
    {
        return $this->penalty;
    }

    /**
     * Set the birth date
     *
     * @param \DateTime $birthDate
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setBirthDate($birthDate)
    {
        $this->birthDate = $birthDate;

        return $this;
    }

    /**
     * Get the birth date
     *
     * @return \DateTime
     */
    public function getBirthDate()
    {
        return $this->birthDate;
    }

    /**
     * Set the notes
     *
     * @param string $notes
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setNotes($notes)
    {
        $this->notes = $notes;

        return $this;
    }

    /**
     * Get the notes
     *
     * @return string
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * Set the created on
     *
     * @param \DateTime $createdOn
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
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
     * Set the createdOn field on persist
     *
     * @ORM\PrePersist
     */
    public function setCreatedOnBeforePersist()
    {
        $this->setCreatedOn(new \DateTime('NOW'));
    }

    /**
     * Set the last modified on
     *
     * @param \DateTime $lastModifiedOn
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
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
     * Set the lastModifiedOn field on persist
     *
     * @ORM\PreUpdate
     */
    public function setLastModifiedOnBeforeUpdate()
    {
        $this->setLastModifiedOn(new \DateTime('NOW'));
    }

    /**
     * Set the version
     *
     * @param int $version
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
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
     * Set the version field on persist
     *
     * @ORM\PrePersist
     */
    public function setVersionBeforePersist()
    {
        $this->setVersion(1);
    }
}

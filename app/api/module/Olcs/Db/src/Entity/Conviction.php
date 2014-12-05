<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Conviction Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @Gedmo\SoftDeleteable(fieldName="deletedDate", timeAware=true)
 * @ORM\Table(name="conviction",
 *    indexes={
 *        @ORM\Index(name="fk_conviction_conviction_category1_idx", columns={"conviction_category"}),
 *        @ORM\Index(name="fk_conviction_person1_idx", columns={"person_id"}),
 *        @ORM\Index(name="fk_conviction_organisation1_idx", columns={"organisation_id"}),
 *        @ORM\Index(name="fk_conviction_transport_manager1_idx", columns={"transport_manager_id"}),
 *        @ORM\Index(name="fk_conviction_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_conviction_user2_idx", columns={"last_modified_by"}),
 *        @ORM\Index(name="fk_conviction_operator_case1_idx", columns={"case_id"}),
 *        @ORM\Index(name="fk_conviction_ref_data1_idx", columns={"defendant_type"})
 *    }
 * )
 */
class Conviction implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\BirthDateField,
        Traits\CategoryText1024Field,
        Traits\CreatedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\CustomDeletedDateField,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CustomLastModifiedOnField,
        Traits\Notes4000Field,
        Traits\OrganisationManyToOne,
        Traits\Penalty255Field,
        Traits\PersonManyToOne,
        Traits\TransportManagerManyToOne,
        Traits\CustomVersionField;

    /**
     * Case
     *
     * @var \Olcs\Db\Entity\Cases
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Cases", inversedBy="convictions")
     * @ORM\JoinColumn(name="case_id", referencedColumnName="id", nullable=false)
     */
    protected $case;

    /**
     * Conviction category
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData")
     * @ORM\JoinColumn(name="conviction_category", referencedColumnName="id", nullable=true)
     */
    protected $convictionCategory;

    /**
     * Conviction date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="conviction_date", nullable=true)
     */
    protected $convictionDate;

    /**
     * Costs
     *
     * @var string
     *
     * @ORM\Column(type="string", name="costs", length=255, nullable=true)
     */
    protected $costs;

    /**
     * Court
     *
     * @var string
     *
     * @ORM\Column(type="string", name="court", length=70, nullable=true)
     */
    protected $court;

    /**
     * Defendant type
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData")
     * @ORM\JoinColumn(name="defendant_type", referencedColumnName="id", nullable=false)
     */
    protected $defendantType;

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
     * Msi
     *
     * @var string
     *
     * @ORM\Column(type="yesnonull", name="msi", nullable=true)
     */
    protected $msi;

    /**
     * Offence date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="offence_date", nullable=true)
     */
    protected $offenceDate;

    /**
     * Operator name
     *
     * @var string
     *
     * @ORM\Column(type="string", name="operator_name", length=70, nullable=true)
     */
    protected $operatorName;

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
     * Taken into consideration
     *
     * @var string
     *
     * @ORM\Column(type="string", name="taken_into_consideration", length=4000, nullable=true)
     */
    protected $takenIntoConsideration;

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
}

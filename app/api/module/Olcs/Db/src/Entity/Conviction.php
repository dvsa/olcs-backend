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
        Traits\IdIdentity,
        Traits\TransportManagerManyToOne,
        Traits\CreatedByManyToOne,
        Traits\LastModifiedByManyToOne,
        Traits\PersonManyToOne,
        Traits\OrganisationManyToOne,
        Traits\Penalty255Field,
        Traits\BirthDateField,
        Traits\Notes4000Field,
        Traits\CustomDeletedDateField,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

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
     * Category text
     *
     * @var string
     *
     * @ORM\Column(type="string", name="category_text", length=1024, nullable=true)
     */
    protected $categoryText;

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
}

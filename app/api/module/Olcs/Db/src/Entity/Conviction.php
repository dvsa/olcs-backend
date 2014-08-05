<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * Conviction Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="conviction",
 *    indexes={
 *        @ORM\Index(name="fk_conviction_conviction_category1_idx", columns={"conviction_category_id"}),
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
        Traits\LastModifiedByManyToOne,
        Traits\CaseManyToOne,
        Traits\CreatedByManyToOne,
        Traits\TransportManagerManyToOne,
        Traits\PersonManyToOne,
        Traits\OrganisationManyToOne,
        Traits\Penalty255Field,
        Traits\Notes4000Field,
        Traits\CategoryText1024Field,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Defendant type
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData")
     * @ORM\JoinColumn(name="defendant_type", referencedColumnName="id")
     */
    protected $defendantType;

    /**
     * Conviction category
     *
     * @var \Olcs\Db\Entity\ConvictionCategory
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\ConvictionCategory")
     * @ORM\JoinColumn(name="conviction_category_id", referencedColumnName="id")
     */
    protected $convictionCategory;

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
     * @var boolean
     *
     * @ORM\Column(type="yesnonull", name="msi", nullable=true)
     */
    protected $msi;

    /**
     * Is declared
     *
     * @var boolean
     *
     * @ORM\Column(type="yesnonull", name="is_declared", nullable=false)
     */
    protected $isDeclared;

    /**
     * Operator name
     *
     * @var string
     *
     * @ORM\Column(type="string", name="operator_name", length=70, nullable=true)
     */
    protected $operatorName;

    /**
     * Date of birth
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="date_of_birth", nullable=true)
     */
    protected $dateOfBirth;

    /**
     * Taken into consideration
     *
     * @var string
     *
     * @ORM\Column(type="string", name="taken_into_consideration", length=4000, nullable=true)
     */
    protected $takenIntoConsideration;

    /**
     * Convicted name
     *
     * @var string
     *
     * @ORM\Column(type="string", name="convicted_name", length=70, nullable=true)
     */
    protected $convictedName;

    /**
     * Set the defendant type
     *
     * @param \Olcs\Db\Entity\RefData $defendantType
     * @return \Olcs\Db\Entity\Conviction
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
     * @param \Olcs\Db\Entity\ConvictionCategory $convictionCategory
     * @return \Olcs\Db\Entity\Conviction
     */
    public function setConvictionCategory($convictionCategory)
    {
        $this->convictionCategory = $convictionCategory;

        return $this;
    }

    /**
     * Get the conviction category
     *
     * @return \Olcs\Db\Entity\ConvictionCategory
     */
    public function getConvictionCategory()
    {
        return $this->convictionCategory;
    }

    /**
     * Set the offence date
     *
     * @param \DateTime $offenceDate
     * @return \Olcs\Db\Entity\Conviction
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
     * @return \Olcs\Db\Entity\Conviction
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
     * @return \Olcs\Db\Entity\Conviction
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
     * @return \Olcs\Db\Entity\Conviction
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
     * @param boolean $msi
     * @return \Olcs\Db\Entity\Conviction
     */
    public function setMsi($msi)
    {
        $this->msi = $msi;

        return $this;
    }

    /**
     * Get the msi
     *
     * @return boolean
     */
    public function getMsi()
    {
        return $this->msi;
    }

    /**
     * Set the is declared
     *
     * @param boolean $isDeclared
     * @return \Olcs\Db\Entity\Conviction
     */
    public function setIsDeclared($isDeclared)
    {
        $this->isDeclared = $isDeclared;

        return $this;
    }

    /**
     * Get the is declared
     *
     * @return boolean
     */
    public function getIsDeclared()
    {
        return $this->isDeclared;
    }

    /**
     * Set the operator name
     *
     * @param string $operatorName
     * @return \Olcs\Db\Entity\Conviction
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
     * Set the date of birth
     *
     * @param \DateTime $dateOfBirth
     * @return \Olcs\Db\Entity\Conviction
     */
    public function setDateOfBirth($dateOfBirth)
    {
        $this->dateOfBirth = $dateOfBirth;

        return $this;
    }

    /**
     * Get the date of birth
     *
     * @return \DateTime
     */
    public function getDateOfBirth()
    {
        return $this->dateOfBirth;
    }

    /**
     * Set the taken into consideration
     *
     * @param string $takenIntoConsideration
     * @return \Olcs\Db\Entity\Conviction
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
     * Set the convicted name
     *
     * @param string $convictedName
     * @return \Olcs\Db\Entity\Conviction
     */
    public function setConvictedName($convictedName)
    {
        $this->convictedName = $convictedName;

        return $this;
    }

    /**
     * Get the convicted name
     *
     * @return string
     */
    public function getConvictedName()
    {
        return $this->convictedName;
    }
}

<?php

namespace Dvsa\Olcs\Api\Entity\Permits;

use Dvsa\Olcs\Api\Domain\QueryHandler\BundleSerializableInterface;
use JsonSerializable;
use Dvsa\Olcs\Api\Entity\Traits\BundleSerializableTrait;
use Dvsa\Olcs\Api\Entity\Traits\ProcessDateTrait;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * MonthlyEcmtPermitApplication Abstract Entity
 *
 * Auto-Generated
 *
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="monthly_ecmt_permit_application",
 *    indexes={
 *        @ORM\Index(name="ix_monthly_ecmt_permit_application_licence_id", columns={"licence_id"}),
 *        @ORM\Index(name="ix_ecmt_permit_application_euro_emission_standard",
     *     columns={"euro_emission_standard"}),
 *        @ORM\Index(name="ix_monthly_ecmt_permit_application_source", columns={"source"}),
 *        @ORM\Index(name="ix_monthly_ecmt_permit_application_status", columns={"status"}),
 *        @ORM\Index(name="ix_monthly_ecmt_permit_application_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_monthly_ecmt_permit_application_last_modified_by",
     *     columns={"last_modified_by"})
 *    }
 * )
 */
abstract class AbstractMonthlyEcmtPermitApplication implements BundleSerializableInterface, JsonSerializable
{
    use BundleSerializableTrait;
    use ProcessDateTrait;

    /**
     * Additional evidence provided
     *
     * @var boolean
     *
     * @ORM\Column(type="boolean", name="additional_evidence_provided", nullable=true)
     */
    protected $additionalEvidenceProvided;

    /**
     * Annual ecmt unsuccessful
     *
     * @var boolean
     *
     * @ORM\Column(type="boolean", name="annual_ecmt_unsuccessful", nullable=true)
     */
    protected $annualEcmtUnsuccessful;

    /**
     * Cabotage
     *
     * @var boolean
     *
     * @ORM\Column(type="boolean", name="cabotage", nullable=true)
     */
    protected $cabotage;

    /**
     * Checked answers
     *
     * @var boolean
     *
     * @ORM\Column(type="boolean", name="checked_answers", nullable=true)
     */
    protected $checkedAnswers;

    /**
     * Created by
     *
     * @var \Dvsa\Olcs\Api\Entity\User\User
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\User\User", fetch="LAZY")
     * @ORM\JoinColumn(name="created_by", referencedColumnName="id", nullable=true)
     * @Gedmo\Blameable(on="create")
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
     * Declaration
     *
     * @var boolean
     *
     * @ORM\Column(type="boolean", name="declaration", nullable=true)
     */
    protected $declaration;

    /**
     * Economic social benefits
     *
     * @var string
     *
     * @ORM\Column(type="string", name="economic_social_benefits", length=3000, nullable=true)
     */
    protected $economicSocialBenefits;

    /**
     * Euro emission standard
     *
     * @var \Dvsa\Olcs\Api\Entity\System\RefData
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\System\RefData", fetch="LAZY")
     * @ORM\JoinColumn(name="euro_emission_standard", referencedColumnName="id", nullable=true)
     */
    protected $euroEmissionStandard;

    /**
     * Has restricted countries
     *
     * @var boolean
     *
     * @ORM\Column(type="boolean", name="has_restricted_countries", nullable=true)
     */
    protected $hasRestrictedCountries;

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
     * @Gedmo\Blameable(on="update")
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
     * @ORM\JoinColumn(name="licence_id", referencedColumnName="id", nullable=true)
     */
    protected $licence;

    /**
     * Monthly permit justification
     *
     * @var string
     *
     * @ORM\Column(type="string", name="monthly_permit_justification", length=3000, nullable=true)
     */
    protected $monthlyPermitJustification;

    /**
     * Other haulage options
     *
     * @var string
     *
     * @ORM\Column(type="string", name="other_haulage_options", length=3000, nullable=true)
     */
    protected $otherHaulageOptions;

    /**
     * Source
     *
     * @var \Dvsa\Olcs\Api\Entity\System\RefData
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\System\RefData", fetch="LAZY")
     * @ORM\JoinColumn(name="source", referencedColumnName="id", nullable=false)
     */
    protected $source;

    /**
     * Start date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="start_date", nullable=true)
     */
    protected $startDate;

    /**
     * Status
     *
     * @var \Dvsa\Olcs\Api\Entity\System\RefData
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\System\RefData", fetch="LAZY")
     * @ORM\JoinColumn(name="status", referencedColumnName="id", nullable=false)
     */
    protected $status;

    /**
     * Urgency reason
     *
     * @var string
     *
     * @ORM\Column(type="string", name="urgency_reason", length=3000, nullable=true)
     */
    protected $urgencyReason;

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
     * Set the additional evidence provided
     *
     * @param boolean $additionalEvidenceProvided new value being set
     *
     * @return MonthlyEcmtPermitApplication
     */
    public function setAdditionalEvidenceProvided($additionalEvidenceProvided)
    {
        $this->additionalEvidenceProvided = $additionalEvidenceProvided;

        return $this;
    }

    /**
     * Get the additional evidence provided
     *
     * @return boolean
     */
    public function getAdditionalEvidenceProvided()
    {
        return $this->additionalEvidenceProvided;
    }

    /**
     * Set the annual ecmt unsuccessful
     *
     * @param boolean $annualEcmtUnsuccessful new value being set
     *
     * @return MonthlyEcmtPermitApplication
     */
    public function setAnnualEcmtUnsuccessful($annualEcmtUnsuccessful)
    {
        $this->annualEcmtUnsuccessful = $annualEcmtUnsuccessful;

        return $this;
    }

    /**
     * Get the annual ecmt unsuccessful
     *
     * @return boolean
     */
    public function getAnnualEcmtUnsuccessful()
    {
        return $this->annualEcmtUnsuccessful;
    }

    /**
     * Set the cabotage
     *
     * @param boolean $cabotage new value being set
     *
     * @return MonthlyEcmtPermitApplication
     */
    public function setCabotage($cabotage)
    {
        $this->cabotage = $cabotage;

        return $this;
    }

    /**
     * Get the cabotage
     *
     * @return boolean
     */
    public function getCabotage()
    {
        return $this->cabotage;
    }

    /**
     * Set the checked answers
     *
     * @param boolean $checkedAnswers new value being set
     *
     * @return MonthlyEcmtPermitApplication
     */
    public function setCheckedAnswers($checkedAnswers)
    {
        $this->checkedAnswers = $checkedAnswers;

        return $this;
    }

    /**
     * Get the checked answers
     *
     * @return boolean
     */
    public function getCheckedAnswers()
    {
        return $this->checkedAnswers;
    }

    /**
     * Set the created by
     *
     * @param \Dvsa\Olcs\Api\Entity\User\User $createdBy entity being set as the value
     *
     * @return MonthlyEcmtPermitApplication
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
     * @param \DateTime $createdOn new value being set
     *
     * @return MonthlyEcmtPermitApplication
     */
    public function setCreatedOn($createdOn)
    {
        $this->createdOn = $createdOn;

        return $this;
    }

    /**
     * Get the created on
     *
     * @param bool $asDateTime If true will always return a \DateTime (or null) never a string datetime
     *
     * @return \DateTime
     */
    public function getCreatedOn($asDateTime = false)
    {
        if ($asDateTime === true) {
            return $this->asDateTime($this->createdOn);
        }

        return $this->createdOn;
    }

    /**
     * Set the declaration
     *
     * @param boolean $declaration new value being set
     *
     * @return MonthlyEcmtPermitApplication
     */
    public function setDeclaration($declaration)
    {
        $this->declaration = $declaration;

        return $this;
    }

    /**
     * Get the declaration
     *
     * @return boolean
     */
    public function getDeclaration()
    {
        return $this->declaration;
    }

    /**
     * Set the economic social benefits
     *
     * @param string $economicSocialBenefits new value being set
     *
     * @return MonthlyEcmtPermitApplication
     */
    public function setEconomicSocialBenefits($economicSocialBenefits)
    {
        $this->economicSocialBenefits = $economicSocialBenefits;

        return $this;
    }

    /**
     * Get the economic social benefits
     *
     * @return string
     */
    public function getEconomicSocialBenefits()
    {
        return $this->economicSocialBenefits;
    }

    /**
     * Set the euro emission standard
     *
     * @param \Dvsa\Olcs\Api\Entity\System\RefData $euroEmissionStandard entity being set as the value
     *
     * @return MonthlyEcmtPermitApplication
     */
    public function setEuroEmissionStandard($euroEmissionStandard)
    {
        $this->euroEmissionStandard = $euroEmissionStandard;

        return $this;
    }

    /**
     * Get the euro emission standard
     *
     * @return \Dvsa\Olcs\Api\Entity\System\RefData
     */
    public function getEuroEmissionStandard()
    {
        return $this->euroEmissionStandard;
    }

    /**
     * Set the has restricted countries
     *
     * @param boolean $hasRestrictedCountries new value being set
     *
     * @return MonthlyEcmtPermitApplication
     */
    public function setHasRestrictedCountries($hasRestrictedCountries)
    {
        $this->hasRestrictedCountries = $hasRestrictedCountries;

        return $this;
    }

    /**
     * Get the has restricted countries
     *
     * @return boolean
     */
    public function getHasRestrictedCountries()
    {
        return $this->hasRestrictedCountries;
    }

    /**
     * Set the id
     *
     * @param int $id new value being set
     *
     * @return MonthlyEcmtPermitApplication
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
     * @param \Dvsa\Olcs\Api\Entity\User\User $lastModifiedBy entity being set as the value
     *
     * @return MonthlyEcmtPermitApplication
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
     * @param \DateTime $lastModifiedOn new value being set
     *
     * @return MonthlyEcmtPermitApplication
     */
    public function setLastModifiedOn($lastModifiedOn)
    {
        $this->lastModifiedOn = $lastModifiedOn;

        return $this;
    }

    /**
     * Get the last modified on
     *
     * @param bool $asDateTime If true will always return a \DateTime (or null) never a string datetime
     *
     * @return \DateTime
     */
    public function getLastModifiedOn($asDateTime = false)
    {
        if ($asDateTime === true) {
            return $this->asDateTime($this->lastModifiedOn);
        }

        return $this->lastModifiedOn;
    }

    /**
     * Set the licence
     *
     * @param \Dvsa\Olcs\Api\Entity\Licence\Licence $licence entity being set as the value
     *
     * @return MonthlyEcmtPermitApplication
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
     * Set the monthly permit justification
     *
     * @param string $monthlyPermitJustification new value being set
     *
     * @return MonthlyEcmtPermitApplication
     */
    public function setMonthlyPermitJustification($monthlyPermitJustification)
    {
        $this->monthlyPermitJustification = $monthlyPermitJustification;

        return $this;
    }

    /**
     * Get the monthly permit justification
     *
     * @return string
     */
    public function getMonthlyPermitJustification()
    {
        return $this->monthlyPermitJustification;
    }

    /**
     * Set the other haulage options
     *
     * @param string $otherHaulageOptions new value being set
     *
     * @return MonthlyEcmtPermitApplication
     */
    public function setOtherHaulageOptions($otherHaulageOptions)
    {
        $this->otherHaulageOptions = $otherHaulageOptions;

        return $this;
    }

    /**
     * Get the other haulage options
     *
     * @return string
     */
    public function getOtherHaulageOptions()
    {
        return $this->otherHaulageOptions;
    }

    /**
     * Set the source
     *
     * @param \Dvsa\Olcs\Api\Entity\System\RefData $source entity being set as the value
     *
     * @return MonthlyEcmtPermitApplication
     */
    public function setSource($source)
    {
        $this->source = $source;

        return $this;
    }

    /**
     * Get the source
     *
     * @return \Dvsa\Olcs\Api\Entity\System\RefData
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Set the start date
     *
     * @param \DateTime $startDate new value being set
     *
     * @return MonthlyEcmtPermitApplication
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * Get the start date
     *
     * @param bool $asDateTime If true will always return a \DateTime (or null) never a string datetime
     *
     * @return \DateTime
     */
    public function getStartDate($asDateTime = false)
    {
        if ($asDateTime === true) {
            return $this->asDateTime($this->startDate);
        }

        return $this->startDate;
    }

    /**
     * Set the status
     *
     * @param \Dvsa\Olcs\Api\Entity\System\RefData $status entity being set as the value
     *
     * @return MonthlyEcmtPermitApplication
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
     * Set the urgency reason
     *
     * @param string $urgencyReason new value being set
     *
     * @return MonthlyEcmtPermitApplication
     */
    public function setUrgencyReason($urgencyReason)
    {
        $this->urgencyReason = $urgencyReason;

        return $this;
    }

    /**
     * Get the urgency reason
     *
     * @return string
     */
    public function getUrgencyReason()
    {
        return $this->urgencyReason;
    }

    /**
     * Set the version
     *
     * @param int $version new value being set
     *
     * @return MonthlyEcmtPermitApplication
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
     *
     * @return void
     */
    public function setCreatedOnBeforePersist()
    {
        $this->createdOn = new \DateTime();
    }

    /**
     * Set the lastModifiedOn field on persist
     *
     * @ORM\PreUpdate
     *
     * @return void
     */
    public function setLastModifiedOnBeforeUpdate()
    {
        $this->lastModifiedOn = new \DateTime();
    }

    /**
     * Clear properties
     *
     * @param array $properties array of properties
     *
     * @return void
     */
    public function clearProperties($properties = array())
    {
        foreach ($properties as $property) {
            if (property_exists($this, $property)) {
                $this->$property = null;
            }
        }
    }
}

<?php

namespace Dvsa\Olcs\Api\Entity\Tm;

use Dvsa\Olcs\Api\Domain\QueryHandler\BundleSerializableInterface;
use JsonSerializable;
use Dvsa\Olcs\Api\Entity\Traits\BundleSerializableTrait;
use Dvsa\Olcs\Api\Entity\Traits\ProcessDateTrait;
use Dvsa\Olcs\Api\Entity\Traits\ClearPropertiesTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * HistoricTm Abstract Entity
 *
 * Auto-Generated
 *
 * @ORM\MappedSuperclass
 * @ORM\Table(name="historic_tm",
 *    indexes={
 *        @ORM\Index(name="ix_historic_tm_birth_date", columns={"birth_date"}),
 *        @ORM\Index(name="ix_historic_tm_historic_id", columns={"historic_id"}),
 *        @ORM\Index(name="ix_historic_tm_family_name", columns={"family_name"}),
 *        @ORM\Index(name="ix_historic_tm_lic_no", columns={"lic_no"}),
 *        @ORM\Index(name="ix_historic_tm_forename", columns={"forename"})
 *    }
 * )
 */
abstract class AbstractHistoricTm implements BundleSerializableInterface, JsonSerializable
{
    use BundleSerializableTrait;
    use ProcessDateTrait;
    use ClearPropertiesTrait;

    /**
     * Application id
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="application_id", nullable=true)
     */
    protected $applicationId;

    /**
     * Birth date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="birth_date", nullable=true)
     */
    protected $birthDate;

    /**
     * Certificate no
     *
     * @var string
     *
     * @ORM\Column(type="string", name="certificate_no", length=45, nullable=true)
     */
    protected $certificateNo;

    /**
     * Date added
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="date_added", nullable=true)
     */
    protected $dateAdded;

    /**
     * Date removed
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="date_removed", nullable=true)
     */
    protected $dateRemoved;

    /**
     * Family name
     *
     * @var string
     *
     * @ORM\Column(type="string", name="family_name", length=45, nullable=true)
     */
    protected $familyName;

    /**
     * Forename
     *
     * @var string
     *
     * @ORM\Column(type="string", name="forename", length=45, nullable=true)
     */
    protected $forename;

    /**
     * Historic id
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="historic_id", nullable=false)
     */
    protected $historicId;

    /**
     * Hours per week
     *
     * @var int
     *
     * @ORM\Column(type="smallint", name="hours_per_week", nullable=true)
     */
    protected $hoursPerWeek;

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
     * Lic no
     *
     * @var string
     *
     * @ORM\Column(type="string", name="lic_no", length=10, nullable=true)
     */
    protected $licNo;

    /**
     * Lic or app
     *
     * @var string
     *
     * @ORM\Column(type="string", name="lic_or_app", length=1, nullable=true)
     */
    protected $licOrApp;

    /**
     * Qualification type
     *
     * @var string
     *
     * @ORM\Column(type="string", name="qualification_type", length=45, nullable=true)
     */
    protected $qualificationType;

    /**
     * Seen contract
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="seen_contract", nullable=false)
     */
    protected $seenContract;

    /**
     * Seen qualification
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="seen_qualification", nullable=false)
     */
    protected $seenQualification;

    /**
     * Set the application id
     *
     * @param int $applicationId new value being set
     *
     * @return HistoricTm
     */
    public function setApplicationId($applicationId)
    {
        $this->applicationId = $applicationId;

        return $this;
    }

    /**
     * Get the application id
     *
     * @return int
     */
    public function getApplicationId()
    {
        return $this->applicationId;
    }

    /**
     * Set the birth date
     *
     * @param \DateTime $birthDate new value being set
     *
     * @return HistoricTm
     */
    public function setBirthDate($birthDate)
    {
        $this->birthDate = $birthDate;

        return $this;
    }

    /**
     * Get the birth date
     *
     * @param bool $asDateTime If true will always return a \DateTime (or null) never a string datetime
     *
     * @return \DateTime
     */
    public function getBirthDate($asDateTime = false)
    {
        if ($asDateTime === true) {
            return $this->asDateTime($this->birthDate);
        }

        return $this->birthDate;
    }

    /**
     * Set the certificate no
     *
     * @param string $certificateNo new value being set
     *
     * @return HistoricTm
     */
    public function setCertificateNo($certificateNo)
    {
        $this->certificateNo = $certificateNo;

        return $this;
    }

    /**
     * Get the certificate no
     *
     * @return string
     */
    public function getCertificateNo()
    {
        return $this->certificateNo;
    }

    /**
     * Set the date added
     *
     * @param \DateTime $dateAdded new value being set
     *
     * @return HistoricTm
     */
    public function setDateAdded($dateAdded)
    {
        $this->dateAdded = $dateAdded;

        return $this;
    }

    /**
     * Get the date added
     *
     * @param bool $asDateTime If true will always return a \DateTime (or null) never a string datetime
     *
     * @return \DateTime
     */
    public function getDateAdded($asDateTime = false)
    {
        if ($asDateTime === true) {
            return $this->asDateTime($this->dateAdded);
        }

        return $this->dateAdded;
    }

    /**
     * Set the date removed
     *
     * @param \DateTime $dateRemoved new value being set
     *
     * @return HistoricTm
     */
    public function setDateRemoved($dateRemoved)
    {
        $this->dateRemoved = $dateRemoved;

        return $this;
    }

    /**
     * Get the date removed
     *
     * @param bool $asDateTime If true will always return a \DateTime (or null) never a string datetime
     *
     * @return \DateTime
     */
    public function getDateRemoved($asDateTime = false)
    {
        if ($asDateTime === true) {
            return $this->asDateTime($this->dateRemoved);
        }

        return $this->dateRemoved;
    }

    /**
     * Set the family name
     *
     * @param string $familyName new value being set
     *
     * @return HistoricTm
     */
    public function setFamilyName($familyName)
    {
        $this->familyName = $familyName;

        return $this;
    }

    /**
     * Get the family name
     *
     * @return string
     */
    public function getFamilyName()
    {
        return $this->familyName;
    }

    /**
     * Set the forename
     *
     * @param string $forename new value being set
     *
     * @return HistoricTm
     */
    public function setForename($forename)
    {
        $this->forename = $forename;

        return $this;
    }

    /**
     * Get the forename
     *
     * @return string
     */
    public function getForename()
    {
        return $this->forename;
    }

    /**
     * Set the historic id
     *
     * @param int $historicId new value being set
     *
     * @return HistoricTm
     */
    public function setHistoricId($historicId)
    {
        $this->historicId = $historicId;

        return $this;
    }

    /**
     * Get the historic id
     *
     * @return int
     */
    public function getHistoricId()
    {
        return $this->historicId;
    }

    /**
     * Set the hours per week
     *
     * @param int $hoursPerWeek new value being set
     *
     * @return HistoricTm
     */
    public function setHoursPerWeek($hoursPerWeek)
    {
        $this->hoursPerWeek = $hoursPerWeek;

        return $this;
    }

    /**
     * Get the hours per week
     *
     * @return int
     */
    public function getHoursPerWeek()
    {
        return $this->hoursPerWeek;
    }

    /**
     * Set the id
     *
     * @param int $id new value being set
     *
     * @return HistoricTm
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
     * Set the lic no
     *
     * @param string $licNo new value being set
     *
     * @return HistoricTm
     */
    public function setLicNo($licNo)
    {
        $this->licNo = $licNo;

        return $this;
    }

    /**
     * Get the lic no
     *
     * @return string
     */
    public function getLicNo()
    {
        return $this->licNo;
    }

    /**
     * Set the lic or app
     *
     * @param string $licOrApp new value being set
     *
     * @return HistoricTm
     */
    public function setLicOrApp($licOrApp)
    {
        $this->licOrApp = $licOrApp;

        return $this;
    }

    /**
     * Get the lic or app
     *
     * @return string
     */
    public function getLicOrApp()
    {
        return $this->licOrApp;
    }

    /**
     * Set the qualification type
     *
     * @param string $qualificationType new value being set
     *
     * @return HistoricTm
     */
    public function setQualificationType($qualificationType)
    {
        $this->qualificationType = $qualificationType;

        return $this;
    }

    /**
     * Get the qualification type
     *
     * @return string
     */
    public function getQualificationType()
    {
        return $this->qualificationType;
    }

    /**
     * Set the seen contract
     *
     * @param string $seenContract new value being set
     *
     * @return HistoricTm
     */
    public function setSeenContract($seenContract)
    {
        $this->seenContract = $seenContract;

        return $this;
    }

    /**
     * Get the seen contract
     *
     * @return string
     */
    public function getSeenContract()
    {
        return $this->seenContract;
    }

    /**
     * Set the seen qualification
     *
     * @param string $seenQualification new value being set
     *
     * @return HistoricTm
     */
    public function setSeenQualification($seenQualification)
    {
        $this->seenQualification = $seenQualification;

        return $this;
    }

    /**
     * Get the seen qualification
     *
     * @return string
     */
    public function getSeenQualification()
    {
        return $this->seenQualification;
    }
}

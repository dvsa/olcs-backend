<?php

namespace Dvsa\Olcs\Api\Entity\Legacy;

use Doctrine\ORM\Mapping as ORM;

/**
 * LegacyOffence Abstract Entity
 *
 * Auto-Generated
 *
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="legacy_offence",
 *    indexes={
 *        @ORM\Index(name="ix_legacy_offence_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_legacy_offence_last_modified_by", columns={"last_modified_by"}),
 *        @ORM\Index(name="fk_legacy_offence_cases1_idx", columns={"case_id"})
 *    }
 * )
 */
abstract class AbstractLegacyOffence
{

    /**
     * Case
     *
     * @var \Dvsa\Olcs\Api\Entity\Cases\Cases
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\Cases\Cases", inversedBy="legacyOffences")
     * @ORM\JoinColumn(name="case_id", referencedColumnName="id", nullable=false)
     */
    protected $case;

    /**
     * Created by
     *
     * @var \Dvsa\Olcs\Api\Entity\User\User
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\User\User")
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
     * Definition
     *
     * @var string
     *
     * @ORM\Column(type="string", name="definition", length=1000, nullable=true)
     */
    protected $definition;

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
     * Is trailer
     *
     * @var string
     *
     * @ORM\Column(type="yesnonull", name="is_trailer", nullable=true)
     */
    protected $isTrailer;

    /**
     * Last modified by
     *
     * @var \Dvsa\Olcs\Api\Entity\User\User
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\User\User")
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
     * Notes
     *
     * @var string
     *
     * @ORM\Column(type="string", name="notes", length=4000, nullable=true)
     */
    protected $notes;

    /**
     * Num of offences
     *
     * @var int
     *
     * @ORM\Column(type="smallint", name="num_of_offences", nullable=true)
     */
    protected $numOfOffences;

    /**
     * Offence authority
     *
     * @var string
     *
     * @ORM\Column(type="string", name="offence_authority", length=100, nullable=true)
     */
    protected $offenceAuthority;

    /**
     * Offence date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="offence_date", nullable=true)
     */
    protected $offenceDate;

    /**
     * Offence to date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="offence_to_date", nullable=true)
     */
    protected $offenceToDate;

    /**
     * Offence type
     *
     * @var string
     *
     * @ORM\Column(type="string", name="offence_type", length=100, nullable=true)
     */
    protected $offenceType;

    /**
     * Offender name
     *
     * @var string
     *
     * @ORM\Column(type="string", name="offender_name", length=100, nullable=true)
     */
    protected $offenderName;

    /**
     * Points
     *
     * @var int
     *
     * @ORM\Column(type="smallint", name="points", nullable=true)
     */
    protected $points;

    /**
     * Position
     *
     * @var string
     *
     * @ORM\Column(type="string", name="position", length=100, nullable=true)
     */
    protected $position;

    /**
     * Version
     *
     * @var int
     *
     * @ORM\Version
     * @ORM\Column(type="smallint", name="version", nullable=false, options={"default": 1})
     */
    protected $version = 1;

    /**
     * Vrm
     *
     * @var string
     *
     * @ORM\Column(type="string", name="vrm", length=20, nullable=true)
     */
    protected $vrm;

    /**
     * Set the case
     *
     * @param \Dvsa\Olcs\Api\Entity\Cases\Cases $case
     * @return LegacyOffence
     */
    public function setCase($case)
    {
        $this->case = $case;

        return $this;
    }

    /**
     * Get the case
     *
     * @return \Dvsa\Olcs\Api\Entity\Cases\Cases
     */
    public function getCase()
    {
        return $this->case;
    }

    /**
     * Set the created by
     *
     * @param \Dvsa\Olcs\Api\Entity\User\User $createdBy
     * @return LegacyOffence
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
     * @return LegacyOffence
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
     * Set the definition
     *
     * @param string $definition
     * @return LegacyOffence
     */
    public function setDefinition($definition)
    {
        $this->definition = $definition;

        return $this;
    }

    /**
     * Get the definition
     *
     * @return string
     */
    public function getDefinition()
    {
        return $this->definition;
    }

    /**
     * Set the id
     *
     * @param int $id
     * @return LegacyOffence
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
     * Set the is trailer
     *
     * @param string $isTrailer
     * @return LegacyOffence
     */
    public function setIsTrailer($isTrailer)
    {
        $this->isTrailer = $isTrailer;

        return $this;
    }

    /**
     * Get the is trailer
     *
     * @return string
     */
    public function getIsTrailer()
    {
        return $this->isTrailer;
    }

    /**
     * Set the last modified by
     *
     * @param \Dvsa\Olcs\Api\Entity\User\User $lastModifiedBy
     * @return LegacyOffence
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
     * @return LegacyOffence
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
     * Set the notes
     *
     * @param string $notes
     * @return LegacyOffence
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
     * Set the num of offences
     *
     * @param int $numOfOffences
     * @return LegacyOffence
     */
    public function setNumOfOffences($numOfOffences)
    {
        $this->numOfOffences = $numOfOffences;

        return $this;
    }

    /**
     * Get the num of offences
     *
     * @return int
     */
    public function getNumOfOffences()
    {
        return $this->numOfOffences;
    }

    /**
     * Set the offence authority
     *
     * @param string $offenceAuthority
     * @return LegacyOffence
     */
    public function setOffenceAuthority($offenceAuthority)
    {
        $this->offenceAuthority = $offenceAuthority;

        return $this;
    }

    /**
     * Get the offence authority
     *
     * @return string
     */
    public function getOffenceAuthority()
    {
        return $this->offenceAuthority;
    }

    /**
     * Set the offence date
     *
     * @param \DateTime $offenceDate
     * @return LegacyOffence
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
     * Set the offence to date
     *
     * @param \DateTime $offenceToDate
     * @return LegacyOffence
     */
    public function setOffenceToDate($offenceToDate)
    {
        $this->offenceToDate = $offenceToDate;

        return $this;
    }

    /**
     * Get the offence to date
     *
     * @return \DateTime
     */
    public function getOffenceToDate()
    {
        return $this->offenceToDate;
    }

    /**
     * Set the offence type
     *
     * @param string $offenceType
     * @return LegacyOffence
     */
    public function setOffenceType($offenceType)
    {
        $this->offenceType = $offenceType;

        return $this;
    }

    /**
     * Get the offence type
     *
     * @return string
     */
    public function getOffenceType()
    {
        return $this->offenceType;
    }

    /**
     * Set the offender name
     *
     * @param string $offenderName
     * @return LegacyOffence
     */
    public function setOffenderName($offenderName)
    {
        $this->offenderName = $offenderName;

        return $this;
    }

    /**
     * Get the offender name
     *
     * @return string
     */
    public function getOffenderName()
    {
        return $this->offenderName;
    }

    /**
     * Set the points
     *
     * @param int $points
     * @return LegacyOffence
     */
    public function setPoints($points)
    {
        $this->points = $points;

        return $this;
    }

    /**
     * Get the points
     *
     * @return int
     */
    public function getPoints()
    {
        return $this->points;
    }

    /**
     * Set the position
     *
     * @param string $position
     * @return LegacyOffence
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get the position
     *
     * @return string
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Set the version
     *
     * @param int $version
     * @return LegacyOffence
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
     * Set the vrm
     *
     * @param string $vrm
     * @return LegacyOffence
     */
    public function setVrm($vrm)
    {
        $this->vrm = $vrm;

        return $this;
    }

    /**
     * Get the vrm
     *
     * @return string
     */
    public function getVrm()
    {
        return $this->vrm;
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

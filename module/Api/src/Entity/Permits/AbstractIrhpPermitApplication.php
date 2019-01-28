<?php

namespace Dvsa\Olcs\Api\Entity\Permits;

use Dvsa\Olcs\Api\Domain\QueryHandler\BundleSerializableInterface;
use JsonSerializable;
use Dvsa\Olcs\Api\Entity\Traits\BundleSerializableTrait;
use Dvsa\Olcs\Api\Entity\Traits\ProcessDateTrait;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * IrhpPermitApplication Abstract Entity
 *
 * Auto-Generated
 *
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="irhp_permit_application",
 *    indexes={
 *        @ORM\Index(name="fk_irhp_permit_applications_irhp_permit_windows1_idx",
     *     columns={"irhp_permit_window_id"}),
 *        @ORM\Index(name="fk_irhp_permit_applications_licence1_idx", columns={"licence_id"}),
 *        @ORM\Index(name="fk_irhp_permit_application_ecmt_permit_application1_idx",
     *     columns={"ecmt_permit_application_id"}),
 *        @ORM\Index(name="fk_irhp_permit_application_sectors_id1_idx", columns={"sectors_id"}),
 *        @ORM\Index(name="irhp_permit_type_ref_data_status_id_fk", columns={"status"}),
 *        @ORM\Index(name="fk_irhp_permit_application_created_by_user_id", columns={"created_by"}),
 *        @ORM\Index(name="fk_irhp_permit_application_last_modified_by_user_id",
     *     columns={"last_modified_by"}),
 *        @ORM\Index(name="fk_irhp_permit_application_irhp_application1",
     *     columns={"irhp_application_id"})
 *    }
 * )
 */
abstract class AbstractIrhpPermitApplication implements BundleSerializableInterface, JsonSerializable
{
    use BundleSerializableTrait;
    use ProcessDateTrait;

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
     * Ecmt permit application
     *
     * @var \Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication
     *
     * @ORM\ManyToOne(
     *     targetEntity="Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication",
     *     fetch="LAZY",
     *     inversedBy="irhpPermitApplications"
     * )
     * @ORM\JoinColumn(name="ecmt_permit_application_id", referencedColumnName="id", nullable=true)
     */
    protected $ecmtPermitApplication;

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
     * Irhp application
     *
     * @var \Dvsa\Olcs\Api\Entity\Permits\IrhpApplication
     *
     * @ORM\ManyToOne(
     *     targetEntity="Dvsa\Olcs\Api\Entity\Permits\IrhpApplication",
     *     fetch="LAZY",
     *     inversedBy="irhpPermitApplications"
     * )
     * @ORM\JoinColumn(name="irhp_application_id", referencedColumnName="id", nullable=true)
     */
    protected $irhpApplication;

    /**
     * Irhp permit window
     *
     * @var \Dvsa\Olcs\Api\Entity\Permits\IrhpPermitWindow
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\Permits\IrhpPermitWindow", fetch="LAZY")
     * @ORM\JoinColumn(name="irhp_permit_window_id", referencedColumnName="id", nullable=false)
     */
    protected $irhpPermitWindow;

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
     * Permits required
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="permits_required", nullable=true)
     */
    protected $permitsRequired;

    /**
     * Properties
     *
     * @var string
     *
     * @ORM\Column(type="text", name="properties", nullable=true)
     */
    protected $properties;

    /**
     * Sectors
     *
     * @var \Dvsa\Olcs\Api\Entity\Permits\Sectors
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\Permits\Sectors", fetch="LAZY")
     * @ORM\JoinColumn(name="sectors_id", referencedColumnName="id", nullable=true)
     */
    protected $sectors;

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
     * @ORM\JoinColumn(name="status", referencedColumnName="id", nullable=true)
     */
    protected $status;

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
     * Answer
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(
     *     targetEntity="Dvsa\Olcs\Api\Entity\Generic\Answer",
     *     mappedBy="irhpPermitApplication"
     * )
     */
    protected $answers;

    /**
     * Irhp candidate permit
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(
     *     targetEntity="Dvsa\Olcs\Api\Entity\Permits\IrhpCandidatePermit",
     *     mappedBy="irhpPermitApplication"
     * )
     */
    protected $irhpCandidatePermits;

    /**
     * Initialise the collections
     *
     * @return void
     */
    public function __construct()
    {
        $this->initCollections();
    }

    /**
     * Initialise the collections
     *
     * @return void
     */
    public function initCollections()
    {
        $this->answers = new ArrayCollection();
        $this->irhpCandidatePermits = new ArrayCollection();
    }

    /**
     * Set the created by
     *
     * @param \Dvsa\Olcs\Api\Entity\User\User $createdBy entity being set as the value
     *
     * @return IrhpPermitApplication
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
     * @return IrhpPermitApplication
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
     * Set the ecmt permit application
     *
     * @param \Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication $ecmtPermitApplication entity being set as the value
     *
     * @return IrhpPermitApplication
     */
    public function setEcmtPermitApplication($ecmtPermitApplication)
    {
        $this->ecmtPermitApplication = $ecmtPermitApplication;

        return $this;
    }

    /**
     * Get the ecmt permit application
     *
     * @return \Dvsa\Olcs\Api\Entity\Permits\EcmtPermitApplication
     */
    public function getEcmtPermitApplication()
    {
        return $this->ecmtPermitApplication;
    }

    /**
     * Set the id
     *
     * @param int $id new value being set
     *
     * @return IrhpPermitApplication
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
     * Set the irhp application
     *
     * @param \Dvsa\Olcs\Api\Entity\Permits\IrhpApplication $irhpApplication entity being set as the value
     *
     * @return IrhpPermitApplication
     */
    public function setIrhpApplication($irhpApplication)
    {
        $this->irhpApplication = $irhpApplication;

        return $this;
    }

    /**
     * Get the irhp application
     *
     * @return \Dvsa\Olcs\Api\Entity\Permits\IrhpApplication
     */
    public function getIrhpApplication()
    {
        return $this->irhpApplication;
    }

    /**
     * Set the irhp permit window
     *
     * @param \Dvsa\Olcs\Api\Entity\Permits\IrhpPermitWindow $irhpPermitWindow entity being set as the value
     *
     * @return IrhpPermitApplication
     */
    public function setIrhpPermitWindow($irhpPermitWindow)
    {
        $this->irhpPermitWindow = $irhpPermitWindow;

        return $this;
    }

    /**
     * Get the irhp permit window
     *
     * @return \Dvsa\Olcs\Api\Entity\Permits\IrhpPermitWindow
     */
    public function getIrhpPermitWindow()
    {
        return $this->irhpPermitWindow;
    }

    /**
     * Set the last modified by
     *
     * @param \Dvsa\Olcs\Api\Entity\User\User $lastModifiedBy entity being set as the value
     *
     * @return IrhpPermitApplication
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
     * @return IrhpPermitApplication
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
     * @return IrhpPermitApplication
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
     * Set the permits required
     *
     * @param int $permitsRequired new value being set
     *
     * @return IrhpPermitApplication
     */
    public function setPermitsRequired($permitsRequired)
    {
        $this->permitsRequired = $permitsRequired;

        return $this;
    }

    /**
     * Get the permits required
     *
     * @return int
     */
    public function getPermitsRequired()
    {
        return $this->permitsRequired;
    }

    /**
     * Set the properties
     *
     * @param string $properties new value being set
     *
     * @return IrhpPermitApplication
     */
    public function setProperties($properties)
    {
        $this->properties = $properties;

        return $this;
    }

    /**
     * Get the properties
     *
     * @return string
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * Set the sectors
     *
     * @param \Dvsa\Olcs\Api\Entity\Permits\Sectors $sectors entity being set as the value
     *
     * @return IrhpPermitApplication
     */
    public function setSectors($sectors)
    {
        $this->sectors = $sectors;

        return $this;
    }

    /**
     * Get the sectors
     *
     * @return \Dvsa\Olcs\Api\Entity\Permits\Sectors
     */
    public function getSectors()
    {
        return $this->sectors;
    }

    /**
     * Set the start date
     *
     * @param \DateTime $startDate new value being set
     *
     * @return IrhpPermitApplication
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
     * @return IrhpPermitApplication
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
     * Set the version
     *
     * @param int $version new value being set
     *
     * @return IrhpPermitApplication
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
     * Set the answer
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $answers collection being set as the value
     *
     * @return IrhpPermitApplication
     */
    public function setAnswers($answers)
    {
        $this->answers = $answers;

        return $this;
    }

    /**
     * Get the answers
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getAnswers()
    {
        return $this->answers;
    }

    /**
     * Add a answers
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $answers collection being added
     *
     * @return IrhpPermitApplication
     */
    public function addAnswers($answers)
    {
        if ($answers instanceof ArrayCollection) {
            $this->answers = new ArrayCollection(
                array_merge(
                    $this->answers->toArray(),
                    $answers->toArray()
                )
            );
        } elseif (!$this->answers->contains($answers)) {
            $this->answers->add($answers);
        }

        return $this;
    }

    /**
     * Remove a answers
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $answers collection being removed
     *
     * @return IrhpPermitApplication
     */
    public function removeAnswers($answers)
    {
        if ($this->answers->contains($answers)) {
            $this->answers->removeElement($answers);
        }

        return $this;
    }

    /**
     * Set the irhp candidate permit
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $irhpCandidatePermits collection being set as the value
     *
     * @return IrhpPermitApplication
     */
    public function setIrhpCandidatePermits($irhpCandidatePermits)
    {
        $this->irhpCandidatePermits = $irhpCandidatePermits;

        return $this;
    }

    /**
     * Get the irhp candidate permits
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getIrhpCandidatePermits()
    {
        return $this->irhpCandidatePermits;
    }

    /**
     * Add a irhp candidate permits
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $irhpCandidatePermits collection being added
     *
     * @return IrhpPermitApplication
     */
    public function addIrhpCandidatePermits($irhpCandidatePermits)
    {
        if ($irhpCandidatePermits instanceof ArrayCollection) {
            $this->irhpCandidatePermits = new ArrayCollection(
                array_merge(
                    $this->irhpCandidatePermits->toArray(),
                    $irhpCandidatePermits->toArray()
                )
            );
        } elseif (!$this->irhpCandidatePermits->contains($irhpCandidatePermits)) {
            $this->irhpCandidatePermits->add($irhpCandidatePermits);
        }

        return $this;
    }

    /**
     * Remove a irhp candidate permits
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $irhpCandidatePermits collection being removed
     *
     * @return IrhpPermitApplication
     */
    public function removeIrhpCandidatePermits($irhpCandidatePermits)
    {
        if ($this->irhpCandidatePermits->contains($irhpCandidatePermits)) {
            $this->irhpCandidatePermits->removeElement($irhpCandidatePermits);
        }

        return $this;
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
                if ($this->$property instanceof Collection) {
                    $this->$property = new ArrayCollection(array());
                } else {
                    $this->$property = null;
                }
            }
        }
    }
}

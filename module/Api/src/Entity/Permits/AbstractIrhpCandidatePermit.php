<?php

namespace Dvsa\Olcs\Api\Entity\Permits;

use Dvsa\Olcs\Api\Domain\QueryHandler\BundleSerializableInterface;
use JsonSerializable;
use Dvsa\Olcs\Api\Entity\Traits\BundleSerializableTrait;
use Dvsa\Olcs\Api\Entity\Traits\ProcessDateTrait;
use Dvsa\Olcs\Api\Entity\Traits\ClearPropertiesWithCollectionsTrait;
use Dvsa\Olcs\Api\Entity\Traits\CreatedOnTrait;
use Dvsa\Olcs\Api\Entity\Traits\ModifiedOnTrait;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * IrhpCandidatePermit Abstract Entity
 *
 * Auto-Generated
 *
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="irhp_candidate_permit",
 *    indexes={
 *        @ORM\Index(name="fk_irhp_candidate_permit_assigned_emissions_cat_ref_data_id",
     *     columns={"assigned_emissions_category"}),
 *        @ORM\Index(name="fk_irhp_candidate_permit_created_by_user_id", columns={"created_by"}),
 *        @ORM\Index(name="fk_irhp_candidate_permit_irhp_permit_range",
     *     columns={"irhp_permit_range_id"}),
 *        @ORM\Index(name="fk_irhp_candidate_permit_last_modified_by_user_id",
     *     columns={"last_modified_by"}),
 *        @ORM\Index(name="fk_irhp_candidate_permit_requested_emissions_cat_ref_data_id",
     *     columns={"requested_emissions_category"}),
 *        @ORM\Index(name="fk_irhp_candidate_permits_irhp_permit_applications1_idx",
     *     columns={"irhp_permit_application_id"})
 *    }
 * )
 */
abstract class AbstractIrhpCandidatePermit implements BundleSerializableInterface, JsonSerializable
{
    use BundleSerializableTrait;
    use ProcessDateTrait;
    use ClearPropertiesWithCollectionsTrait;
    use CreatedOnTrait;
    use ModifiedOnTrait;

    /**
     * Application score
     *
     * @var float
     *
     * @ORM\Column(type="decimal", name="application_score", precision=18, scale=9, nullable=true)
     */
    protected $applicationScore;

    /**
     * Assigned emissions category
     *
     * @var \Dvsa\Olcs\Api\Entity\System\RefData
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\System\RefData", fetch="LAZY")
     * @ORM\JoinColumn(name="assigned_emissions_category", referencedColumnName="id", nullable=true)
     */
    protected $assignedEmissionsCategory;

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
     * Intensity of use
     *
     * @var float
     *
     * @ORM\Column(type="decimal", name="intensity_of_use", precision=18, scale=9, nullable=true)
     */
    protected $intensityOfUse;

    /**
     * Irhp permit application
     *
     * @var \Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication
     *
     * @ORM\ManyToOne(
     *     targetEntity="Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication",
     *     fetch="LAZY",
     *     inversedBy="irhpCandidatePermits"
     * )
     * @ORM\JoinColumn(name="irhp_permit_application_id", referencedColumnName="id", nullable=false)
     */
    protected $irhpPermitApplication;

    /**
     * Irhp permit range
     *
     * @var \Dvsa\Olcs\Api\Entity\Permits\IrhpPermitRange
     *
     * @ORM\ManyToOne(
     *     targetEntity="Dvsa\Olcs\Api\Entity\Permits\IrhpPermitRange",
     *     fetch="LAZY",
     *     inversedBy="irhpCandidatePermits"
     * )
     * @ORM\JoinColumn(name="irhp_permit_range_id", referencedColumnName="id", nullable=true)
     */
    protected $irhpPermitRange;

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
     * Random factor
     *
     * @var float
     *
     * @ORM\Column(type="decimal", name="random_factor", precision=18, scale=9, nullable=true)
     */
    protected $randomFactor;

    /**
     * Randomized score
     *
     * @var float
     *
     * @ORM\Column(type="decimal", name="randomized_score", precision=18, scale=9, nullable=true)
     */
    protected $randomizedScore;

    /**
     * Requested emissions category
     *
     * @var \Dvsa\Olcs\Api\Entity\System\RefData
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\System\RefData", fetch="LAZY")
     * @ORM\JoinColumn(name="requested_emissions_category", referencedColumnName="id", nullable=true)
     */
    protected $requestedEmissionsCategory;

    /**
     * Successful
     *
     * @var boolean
     *
     * @ORM\Column(type="boolean", name="successful", nullable=true, options={"default": 0})
     */
    protected $successful = 0;

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
     * Wanted
     *
     * @var boolean
     *
     * @ORM\Column(type="boolean", name="wanted", nullable=true, options={"default": 0})
     */
    protected $wanted = 0;

    /**
     * Irhp permit
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(
     *     targetEntity="Dvsa\Olcs\Api\Entity\Permits\IrhpPermit",
     *     mappedBy="irhpCandidatePermit"
     * )
     */
    protected $irhpPermits;

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
        $this->irhpPermits = new ArrayCollection();
    }

    /**
     * Set the application score
     *
     * @param float $applicationScore new value being set
     *
     * @return IrhpCandidatePermit
     */
    public function setApplicationScore($applicationScore)
    {
        $this->applicationScore = $applicationScore;

        return $this;
    }

    /**
     * Get the application score
     *
     * @return float
     */
    public function getApplicationScore()
    {
        return $this->applicationScore;
    }

    /**
     * Set the assigned emissions category
     *
     * @param \Dvsa\Olcs\Api\Entity\System\RefData $assignedEmissionsCategory entity being set as the value
     *
     * @return IrhpCandidatePermit
     */
    public function setAssignedEmissionsCategory($assignedEmissionsCategory)
    {
        $this->assignedEmissionsCategory = $assignedEmissionsCategory;

        return $this;
    }

    /**
     * Get the assigned emissions category
     *
     * @return \Dvsa\Olcs\Api\Entity\System\RefData
     */
    public function getAssignedEmissionsCategory()
    {
        return $this->assignedEmissionsCategory;
    }

    /**
     * Set the created by
     *
     * @param \Dvsa\Olcs\Api\Entity\User\User $createdBy entity being set as the value
     *
     * @return IrhpCandidatePermit
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
     * Set the id
     *
     * @param int $id new value being set
     *
     * @return IrhpCandidatePermit
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
     * Set the intensity of use
     *
     * @param float $intensityOfUse new value being set
     *
     * @return IrhpCandidatePermit
     */
    public function setIntensityOfUse($intensityOfUse)
    {
        $this->intensityOfUse = $intensityOfUse;

        return $this;
    }

    /**
     * Get the intensity of use
     *
     * @return float
     */
    public function getIntensityOfUse()
    {
        return $this->intensityOfUse;
    }

    /**
     * Set the irhp permit application
     *
     * @param \Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication $irhpPermitApplication entity being set as the value
     *
     * @return IrhpCandidatePermit
     */
    public function setIrhpPermitApplication($irhpPermitApplication)
    {
        $this->irhpPermitApplication = $irhpPermitApplication;

        return $this;
    }

    /**
     * Get the irhp permit application
     *
     * @return \Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication
     */
    public function getIrhpPermitApplication()
    {
        return $this->irhpPermitApplication;
    }

    /**
     * Set the irhp permit range
     *
     * @param \Dvsa\Olcs\Api\Entity\Permits\IrhpPermitRange $irhpPermitRange entity being set as the value
     *
     * @return IrhpCandidatePermit
     */
    public function setIrhpPermitRange($irhpPermitRange)
    {
        $this->irhpPermitRange = $irhpPermitRange;

        return $this;
    }

    /**
     * Get the irhp permit range
     *
     * @return \Dvsa\Olcs\Api\Entity\Permits\IrhpPermitRange
     */
    public function getIrhpPermitRange()
    {
        return $this->irhpPermitRange;
    }

    /**
     * Set the last modified by
     *
     * @param \Dvsa\Olcs\Api\Entity\User\User $lastModifiedBy entity being set as the value
     *
     * @return IrhpCandidatePermit
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
     * Set the random factor
     *
     * @param float $randomFactor new value being set
     *
     * @return IrhpCandidatePermit
     */
    public function setRandomFactor($randomFactor)
    {
        $this->randomFactor = $randomFactor;

        return $this;
    }

    /**
     * Get the random factor
     *
     * @return float
     */
    public function getRandomFactor()
    {
        return $this->randomFactor;
    }

    /**
     * Set the randomized score
     *
     * @param float $randomizedScore new value being set
     *
     * @return IrhpCandidatePermit
     */
    public function setRandomizedScore($randomizedScore)
    {
        $this->randomizedScore = $randomizedScore;

        return $this;
    }

    /**
     * Get the randomized score
     *
     * @return float
     */
    public function getRandomizedScore()
    {
        return $this->randomizedScore;
    }

    /**
     * Set the requested emissions category
     *
     * @param \Dvsa\Olcs\Api\Entity\System\RefData $requestedEmissionsCategory entity being set as the value
     *
     * @return IrhpCandidatePermit
     */
    public function setRequestedEmissionsCategory($requestedEmissionsCategory)
    {
        $this->requestedEmissionsCategory = $requestedEmissionsCategory;

        return $this;
    }

    /**
     * Get the requested emissions category
     *
     * @return \Dvsa\Olcs\Api\Entity\System\RefData
     */
    public function getRequestedEmissionsCategory()
    {
        return $this->requestedEmissionsCategory;
    }

    /**
     * Set the successful
     *
     * @param boolean $successful new value being set
     *
     * @return IrhpCandidatePermit
     */
    public function setSuccessful($successful)
    {
        $this->successful = $successful;

        return $this;
    }

    /**
     * Get the successful
     *
     * @return boolean
     */
    public function getSuccessful()
    {
        return $this->successful;
    }

    /**
     * Set the version
     *
     * @param int $version new value being set
     *
     * @return IrhpCandidatePermit
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
     * Set the wanted
     *
     * @param boolean $wanted new value being set
     *
     * @return IrhpCandidatePermit
     */
    public function setWanted($wanted)
    {
        $this->wanted = $wanted;

        return $this;
    }

    /**
     * Get the wanted
     *
     * @return boolean
     */
    public function getWanted()
    {
        return $this->wanted;
    }

    /**
     * Set the irhp permit
     *
     * @param ArrayCollection $irhpPermits collection being set as the value
     *
     * @return IrhpCandidatePermit
     */
    public function setIrhpPermits($irhpPermits)
    {
        $this->irhpPermits = $irhpPermits;

        return $this;
    }

    /**
     * Get the irhp permits
     *
     * @return ArrayCollection
     */
    public function getIrhpPermits()
    {
        return $this->irhpPermits;
    }

    /**
     * Add a irhp permits
     *
     * @param ArrayCollection|mixed $irhpPermits collection being added
     *
     * @return IrhpCandidatePermit
     */
    public function addIrhpPermits($irhpPermits)
    {
        if ($irhpPermits instanceof ArrayCollection) {
            $this->irhpPermits = new ArrayCollection(
                array_merge(
                    $this->irhpPermits->toArray(),
                    $irhpPermits->toArray()
                )
            );
        } elseif (!$this->irhpPermits->contains($irhpPermits)) {
            $this->irhpPermits->add($irhpPermits);
        }

        return $this;
    }

    /**
     * Remove a irhp permits
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $irhpPermits collection being removed
     *
     * @return IrhpCandidatePermit
     */
    public function removeIrhpPermits($irhpPermits)
    {
        if ($this->irhpPermits->contains($irhpPermits)) {
            $this->irhpPermits->removeElement($irhpPermits);
        }

        return $this;
    }
}

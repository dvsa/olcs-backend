<?php

namespace Dvsa\Olcs\Api\Entity\Application;

use Dvsa\Olcs\Api\Domain\QueryHandler\BundleSerializableInterface;
use JsonSerializable;
use Dvsa\Olcs\Api\Entity\Traits\BundleSerializableTrait;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * ApplicationOperatingCentre Abstract Entity
 *
 * Auto-Generated
 *
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 * @Gedmo\SoftDeleteable(fieldName="deletedDate", timeAware=true)
 * @ORM\Table(name="application_operating_centre",
 *    indexes={
 *        @ORM\Index(name="ix_application_operating_centre_application_id",
     *     columns={"application_id"}),
 *        @ORM\Index(name="ix_application_operating_centre_operating_centre_id",
     *     columns={"operating_centre_id"}),
 *        @ORM\Index(name="ix_application_operating_centre_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_application_operating_centre_last_modified_by",
     *     columns={"last_modified_by"}),
 *        @ORM\Index(name="ix_application_operating_centre_s4_id", columns={"s4_id"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="uk_application_operating_centre_olbs_key", columns={"olbs_key"})
 *    }
 * )
 */
abstract class AbstractApplicationOperatingCentre implements BundleSerializableInterface, JsonSerializable
{
    use BundleSerializableTrait;

    /**
     * Action
     *
     * @var string
     *
     * @ORM\Column(type="string", name="action", length=1, nullable=true)
     */
    protected $action;

    /**
     * Ad placed
     *
     * @var int
     *
     * @ORM\Column(type="smallint", name="ad_placed", nullable=false)
     */
    protected $adPlaced;

    /**
     * Ad placed date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="ad_placed_date", nullable=true)
     */
    protected $adPlacedDate;

    /**
     * Ad placed in
     *
     * @var string
     *
     * @ORM\Column(type="string", name="ad_placed_in", length=70, nullable=true)
     */
    protected $adPlacedIn;

    /**
     * Application
     *
     * @var \Dvsa\Olcs\Api\Entity\Application\Application
     *
     * @ORM\ManyToOne(
     *     targetEntity="Dvsa\Olcs\Api\Entity\Application\Application",
     *     fetch="LAZY",
     *     inversedBy="operatingCentres"
     * )
     * @ORM\JoinColumn(name="application_id", referencedColumnName="id", nullable=false)
     */
    protected $application;

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
     * Deleted date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="deleted_date", nullable=true)
     */
    protected $deletedDate;

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
     * Is interim
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="is_interim", nullable=false, options={"default": 0})
     */
    protected $isInterim = 0;

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
     * No of trailers required
     *
     * @var int
     *
     * @ORM\Column(type="smallint", name="no_of_trailers_required", nullable=true)
     */
    protected $noOfTrailersRequired;

    /**
     * No of vehicles required
     *
     * @var int
     *
     * @ORM\Column(type="smallint", name="no_of_vehicles_required", nullable=true)
     */
    protected $noOfVehiclesRequired;

    /**
     * Olbs key
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="olbs_key", nullable=true)
     */
    protected $olbsKey;

    /**
     * Operating centre
     *
     * @var \Dvsa\Olcs\Api\Entity\OperatingCentre\OperatingCentre
     *
     * @ORM\ManyToOne(
     *     targetEntity="Dvsa\Olcs\Api\Entity\OperatingCentre\OperatingCentre",
     *     fetch="LAZY",
     *     inversedBy="applications"
     * )
     * @ORM\JoinColumn(name="operating_centre_id", referencedColumnName="id", nullable=false)
     */
    protected $operatingCentre;

    /**
     * Permission
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="permission", nullable=false)
     */
    protected $permission;

    /**
     * Publication appropriate
     *
     * @var string
     *
     * @ORM\Column(type="yesno",
     *     name="publication_appropriate",
     *     nullable=false,
     *     options={"default": 0})
     */
    protected $publicationAppropriate = 0;

    /**
     * S4
     *
     * @var \Dvsa\Olcs\Api\Entity\Application\S4
     *
     * @ORM\ManyToOne(
     *     targetEntity="Dvsa\Olcs\Api\Entity\Application\S4",
     *     fetch="LAZY",
     *     inversedBy="aocs"
     * )
     * @ORM\JoinColumn(name="s4_id", referencedColumnName="id", nullable=true)
     */
    protected $s4;

    /**
     * Sufficient parking
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="sufficient_parking", nullable=false, options={"default": 0})
     */
    protected $sufficientParking = 0;

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
     * Vi action
     *
     * @var string
     *
     * @ORM\Column(type="string", name="vi_action", length=1, nullable=true)
     */
    protected $viAction;

    /**
     * Set the action
     *
     * @param string $action new value being set
     *
     * @return ApplicationOperatingCentre
     */
    public function setAction($action)
    {
        $this->action = $action;

        return $this;
    }

    /**
     * Get the action
     *
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Set the ad placed
     *
     * @param int $adPlaced new value being set
     *
     * @return ApplicationOperatingCentre
     */
    public function setAdPlaced($adPlaced)
    {
        $this->adPlaced = $adPlaced;

        return $this;
    }

    /**
     * Get the ad placed
     *
     * @return int
     */
    public function getAdPlaced()
    {
        return $this->adPlaced;
    }

    /**
     * Set the ad placed date
     *
     * @param \DateTime $adPlacedDate new value being set
     *
     * @return ApplicationOperatingCentre
     */
    public function setAdPlacedDate($adPlacedDate)
    {
        $this->adPlacedDate = $adPlacedDate;

        return $this;
    }

    /**
     * Get the ad placed date
     *
     * @return \DateTime
     */
    public function getAdPlacedDate()
    {
        return $this->adPlacedDate;
    }

    /**
     * Set the ad placed in
     *
     * @param string $adPlacedIn new value being set
     *
     * @return ApplicationOperatingCentre
     */
    public function setAdPlacedIn($adPlacedIn)
    {
        $this->adPlacedIn = $adPlacedIn;

        return $this;
    }

    /**
     * Get the ad placed in
     *
     * @return string
     */
    public function getAdPlacedIn()
    {
        return $this->adPlacedIn;
    }

    /**
     * Set the application
     *
     * @param \Dvsa\Olcs\Api\Entity\Application\Application $application entity being set as the value
     *
     * @return ApplicationOperatingCentre
     */
    public function setApplication($application)
    {
        $this->application = $application;

        return $this;
    }

    /**
     * Get the application
     *
     * @return \Dvsa\Olcs\Api\Entity\Application\Application
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     * Set the created by
     *
     * @param \Dvsa\Olcs\Api\Entity\User\User $createdBy entity being set as the value
     *
     * @return ApplicationOperatingCentre
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
     * @return ApplicationOperatingCentre
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
     * Set the deleted date
     *
     * @param \DateTime $deletedDate new value being set
     *
     * @return ApplicationOperatingCentre
     */
    public function setDeletedDate($deletedDate)
    {
        $this->deletedDate = $deletedDate;

        return $this;
    }

    /**
     * Get the deleted date
     *
     * @return \DateTime
     */
    public function getDeletedDate()
    {
        return $this->deletedDate;
    }

    /**
     * Set the id
     *
     * @param int $id new value being set
     *
     * @return ApplicationOperatingCentre
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
     * Set the is interim
     *
     * @param string $isInterim new value being set
     *
     * @return ApplicationOperatingCentre
     */
    public function setIsInterim($isInterim)
    {
        $this->isInterim = $isInterim;

        return $this;
    }

    /**
     * Get the is interim
     *
     * @return string
     */
    public function getIsInterim()
    {
        return $this->isInterim;
    }

    /**
     * Set the last modified by
     *
     * @param \Dvsa\Olcs\Api\Entity\User\User $lastModifiedBy entity being set as the value
     *
     * @return ApplicationOperatingCentre
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
     * @return ApplicationOperatingCentre
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
     * Set the no of trailers required
     *
     * @param int $noOfTrailersRequired new value being set
     *
     * @return ApplicationOperatingCentre
     */
    public function setNoOfTrailersRequired($noOfTrailersRequired)
    {
        $this->noOfTrailersRequired = $noOfTrailersRequired;

        return $this;
    }

    /**
     * Get the no of trailers required
     *
     * @return int
     */
    public function getNoOfTrailersRequired()
    {
        return $this->noOfTrailersRequired;
    }

    /**
     * Set the no of vehicles required
     *
     * @param int $noOfVehiclesRequired new value being set
     *
     * @return ApplicationOperatingCentre
     */
    public function setNoOfVehiclesRequired($noOfVehiclesRequired)
    {
        $this->noOfVehiclesRequired = $noOfVehiclesRequired;

        return $this;
    }

    /**
     * Get the no of vehicles required
     *
     * @return int
     */
    public function getNoOfVehiclesRequired()
    {
        return $this->noOfVehiclesRequired;
    }

    /**
     * Set the olbs key
     *
     * @param int $olbsKey new value being set
     *
     * @return ApplicationOperatingCentre
     */
    public function setOlbsKey($olbsKey)
    {
        $this->olbsKey = $olbsKey;

        return $this;
    }

    /**
     * Get the olbs key
     *
     * @return int
     */
    public function getOlbsKey()
    {
        return $this->olbsKey;
    }

    /**
     * Set the operating centre
     *
     * @param \Dvsa\Olcs\Api\Entity\OperatingCentre\OperatingCentre $operatingCentre entity being set as the value
     *
     * @return ApplicationOperatingCentre
     */
    public function setOperatingCentre($operatingCentre)
    {
        $this->operatingCentre = $operatingCentre;

        return $this;
    }

    /**
     * Get the operating centre
     *
     * @return \Dvsa\Olcs\Api\Entity\OperatingCentre\OperatingCentre
     */
    public function getOperatingCentre()
    {
        return $this->operatingCentre;
    }

    /**
     * Set the permission
     *
     * @param string $permission new value being set
     *
     * @return ApplicationOperatingCentre
     */
    public function setPermission($permission)
    {
        $this->permission = $permission;

        return $this;
    }

    /**
     * Get the permission
     *
     * @return string
     */
    public function getPermission()
    {
        return $this->permission;
    }

    /**
     * Set the publication appropriate
     *
     * @param string $publicationAppropriate new value being set
     *
     * @return ApplicationOperatingCentre
     */
    public function setPublicationAppropriate($publicationAppropriate)
    {
        $this->publicationAppropriate = $publicationAppropriate;

        return $this;
    }

    /**
     * Get the publication appropriate
     *
     * @return string
     */
    public function getPublicationAppropriate()
    {
        return $this->publicationAppropriate;
    }

    /**
     * Set the s4
     *
     * @param \Dvsa\Olcs\Api\Entity\Application\S4 $s4 entity being set as the value
     *
     * @return ApplicationOperatingCentre
     */
    public function setS4($s4)
    {
        $this->s4 = $s4;

        return $this;
    }

    /**
     * Get the s4
     *
     * @return \Dvsa\Olcs\Api\Entity\Application\S4
     */
    public function getS4()
    {
        return $this->s4;
    }

    /**
     * Set the sufficient parking
     *
     * @param string $sufficientParking new value being set
     *
     * @return ApplicationOperatingCentre
     */
    public function setSufficientParking($sufficientParking)
    {
        $this->sufficientParking = $sufficientParking;

        return $this;
    }

    /**
     * Get the sufficient parking
     *
     * @return string
     */
    public function getSufficientParking()
    {
        return $this->sufficientParking;
    }

    /**
     * Set the version
     *
     * @param int $version new value being set
     *
     * @return ApplicationOperatingCentre
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
     * Set the vi action
     *
     * @param string $viAction new value being set
     *
     * @return ApplicationOperatingCentre
     */
    public function setViAction($viAction)
    {
        $this->viAction = $viAction;

        return $this;
    }

    /**
     * Get the vi action
     *
     * @return string
     */
    public function getViAction()
    {
        return $this->viAction;
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

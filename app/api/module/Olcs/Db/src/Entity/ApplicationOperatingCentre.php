<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * ApplicationOperatingCentre Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @Gedmo\SoftDeleteable(fieldName="deletedDate", timeAware=true)
 * @ORM\Table(name="application_operating_centre",
 *    indexes={
 *        @ORM\Index(name="fk_ApplicationOperatingCentre_Application1_idx", columns={"application_id"}),
 *        @ORM\Index(name="fk_ApplicationOperatingCentre_OperatingCentre1_idx", columns={"operating_centre_id"}),
 *        @ORM\Index(name="fk_application_operating_centre_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_application_operating_centre_user2_idx", columns={"last_modified_by"}),
 *        @ORM\Index(name="fk_application_operating_centre_s41_idx", columns={"s4_id"})
 *    }
 * )
 */
class ApplicationOperatingCentre implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CreatedByManyToOne,
        Traits\S4ManyToOne,
        Traits\OperatingCentreManyToOne,
        Traits\CustomDeletedDateField,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Application
     *
     * @var \Olcs\Db\Entity\Application
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Application", fetch="LAZY", inversedBy="operatingCentres")
     * @ORM\JoinColumn(name="application_id", referencedColumnName="id", nullable=false)
     */
    protected $application;

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
     * @var string
     *
     * @ORM\Column(type="yesno", name="ad_placed", nullable=false)
     */
    protected $adPlaced;

    /**
     * Ad placed in
     *
     * @var string
     *
     * @ORM\Column(type="string", name="ad_placed_in", length=70, nullable=true)
     */
    protected $adPlacedIn;

    /**
     * Ad placed date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="ad_placed_date", nullable=true)
     */
    protected $adPlacedDate;

    /**
     * Publication appropriate
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="publication_appropriate", nullable=false)
     */
    protected $publicationAppropriate = 0;

    /**
     * Permission
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="permission", nullable=false)
     */
    protected $permission;

    /**
     * Sufficient parking
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="sufficient_parking", nullable=false)
     */
    protected $sufficientParking = 0;

    /**
     * No of trailers required
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="no_of_trailers_required", nullable=true)
     */
    protected $noOfTrailersRequired;

    /**
     * No of vehicles required
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="no_of_vehicles_required", nullable=true)
     */
    protected $noOfVehiclesRequired;

    /**
     * Vi action
     *
     * @var string
     *
     * @ORM\Column(type="string", name="vi_action", length=1, nullable=true)
     */
    protected $viAction;

    /**
     * Is interim
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="is_interim", nullable=false)
     */
    protected $isInterim = 0;

    /**
     * Set the application
     *
     * @param \Olcs\Db\Entity\Application $application
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
     * @return \Olcs\Db\Entity\Application
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     * Set the action
     *
     * @param string $action
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
     * @param string $adPlaced
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
     * @return string
     */
    public function getAdPlaced()
    {
        return $this->adPlaced;
    }

    /**
     * Set the ad placed in
     *
     * @param string $adPlacedIn
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
     * Set the ad placed date
     *
     * @param \DateTime $adPlacedDate
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
     * Set the publication appropriate
     *
     * @param string $publicationAppropriate
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
     * Set the permission
     *
     * @param string $permission
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
     * Set the sufficient parking
     *
     * @param string $sufficientParking
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
     * Set the no of trailers required
     *
     * @param int $noOfTrailersRequired
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
     * @param int $noOfVehiclesRequired
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
     * Set the vi action
     *
     * @param string $viAction
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
     * Set the is interim
     *
     * @param string $isInterim
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
}

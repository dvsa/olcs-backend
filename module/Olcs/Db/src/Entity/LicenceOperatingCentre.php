<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * LicenceOperatingCentre Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @Gedmo\SoftDeleteable(fieldName="deletedDate", timeAware=true)
 * @ORM\Table(name="licence_operating_centre",
 *    indexes={
 *        @ORM\Index(name="fk_LicenceOperatingCentre_licence_idx", columns={"licence_id"}),
 *        @ORM\Index(name="fk_LicenceOperatingCentre_OperatingCentre1_idx", columns={"operating_centre_id"}),
 *        @ORM\Index(name="fk_licence_operating_centre_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_licence_operating_centre_user2_idx", columns={"last_modified_by"}),
 *        @ORM\Index(name="fk_licence_operating_centre_s41_idx", columns={"s4_id"})
 *    }
 * )
 */
class LicenceOperatingCentre implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CreatedByManyToOne,
        Traits\S4ManyToOne,
        Traits\LicenceManyToOne,
        Traits\OperatingCentreManyToOne,
        Traits\ViAction1Field,
        Traits\CustomDeletedDateField,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

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
     * Sufficient parking
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="sufficient_parking", nullable=false)
     */
    protected $sufficientParking;

    /**
     * Permission
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="permission", nullable=false)
     */
    protected $permission;

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
     * No of vehicles possessed
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="no_of_vehicles_possessed", nullable=true)
     */
    protected $noOfVehiclesPossessed;

    /**
     * No of trailers possessed
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="no_of_trailers_possessed", nullable=true)
     */
    protected $noOfTrailersPossessed;

    /**
     * Is interim
     *
     * @var string
     *
     * @ORM\Column(type="yesnonull", name="is_interim", nullable=true)
     */
    protected $isInterim;

    /**
     * Publication appropriate
     *
     * @var string
     *
     * @ORM\Column(type="yesnonull", name="publication_appropriate", nullable=true)
     */
    protected $publicationAppropriate;

    /**
     * Set the ad placed
     *
     * @param string $adPlaced
     * @return LicenceOperatingCentre
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
     * @return LicenceOperatingCentre
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
     * @return LicenceOperatingCentre
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
     * Set the sufficient parking
     *
     * @param string $sufficientParking
     * @return LicenceOperatingCentre
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
     * Set the permission
     *
     * @param string $permission
     * @return LicenceOperatingCentre
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
     * Set the no of trailers required
     *
     * @param int $noOfTrailersRequired
     * @return LicenceOperatingCentre
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
     * @return LicenceOperatingCentre
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
     * Set the no of vehicles possessed
     *
     * @param int $noOfVehiclesPossessed
     * @return LicenceOperatingCentre
     */
    public function setNoOfVehiclesPossessed($noOfVehiclesPossessed)
    {
        $this->noOfVehiclesPossessed = $noOfVehiclesPossessed;

        return $this;
    }

    /**
     * Get the no of vehicles possessed
     *
     * @return int
     */
    public function getNoOfVehiclesPossessed()
    {
        return $this->noOfVehiclesPossessed;
    }

    /**
     * Set the no of trailers possessed
     *
     * @param int $noOfTrailersPossessed
     * @return LicenceOperatingCentre
     */
    public function setNoOfTrailersPossessed($noOfTrailersPossessed)
    {
        $this->noOfTrailersPossessed = $noOfTrailersPossessed;

        return $this;
    }

    /**
     * Get the no of trailers possessed
     *
     * @return int
     */
    public function getNoOfTrailersPossessed()
    {
        return $this->noOfTrailersPossessed;
    }

    /**
     * Set the is interim
     *
     * @param string $isInterim
     * @return LicenceOperatingCentre
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
     * Set the publication appropriate
     *
     * @param string $publicationAppropriate
     * @return LicenceOperatingCentre
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
}

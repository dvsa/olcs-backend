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
        Traits\AdPlacedField,
        Traits\AdPlacedDateField,
        Traits\AdPlacedIn70Field,
        Traits\CreatedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\CustomDeletedDateField,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CustomLastModifiedOnField,
        Traits\LicenceManyToOne,
        Traits\NoOfTrailersRequiredField,
        Traits\NoOfVehiclesRequiredField,
        Traits\OperatingCentreManyToOne,
        Traits\PermissionField,
        Traits\S4ManyToOne,
        Traits\CustomVersionField,
        Traits\ViAction1Field;

    /**
     * Is interim
     *
     * @var string
     *
     * @ORM\Column(type="yesnonull", name="is_interim", nullable=true)
     */
    protected $isInterim;

    /**
     * No of trailers possessed
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="no_of_trailers_possessed", nullable=true)
     */
    protected $noOfTrailersPossessed;

    /**
     * No of vehicles possessed
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="no_of_vehicles_possessed", nullable=true)
     */
    protected $noOfVehiclesPossessed;

    /**
     * Publication appropriate
     *
     * @var string
     *
     * @ORM\Column(type="yesnonull", name="publication_appropriate", nullable=true)
     */
    protected $publicationAppropriate;

    /**
     * Sufficient parking
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="sufficient_parking", nullable=false)
     */
    protected $sufficientParking;

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
}

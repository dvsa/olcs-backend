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
 *        @ORM\Index(name="ix_licence_operating_centre_licence_id", columns={"licence_id"}),
 *        @ORM\Index(name="ix_licence_operating_centre_operating_centre_id", columns={"operating_centre_id"}),
 *        @ORM\Index(name="ix_licence_operating_centre_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_licence_operating_centre_last_modified_by", columns={"last_modified_by"}),
 *        @ORM\Index(name="ix_licence_operating_centre_s4_id", columns={"s4_id"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="uk_licence_operating_centre_olbs_key", columns={"olbs_key"})
 *    }
 * )
 */
class LicenceOperatingCentre implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\AdPlacedDateField,
        Traits\AdPlacedIn70Field,
        Traits\CreatedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\CustomDeletedDateField,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CustomLastModifiedOnField,
        Traits\NoOfTrailersRequiredField,
        Traits\NoOfVehiclesRequiredField,
        Traits\OlbsKeyField,
        Traits\OperatingCentreManyToOne,
        Traits\PermissionField,
        Traits\S4ManyToOne,
        Traits\CustomVersionField,
        Traits\ViAction1Field;

    /**
     * Ad placed
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="ad_placed", nullable=false)
     */
    protected $adPlaced;

    /**
     * Is interim
     *
     * @var string
     *
     * @ORM\Column(type="yesnonull", name="is_interim", nullable=true)
     */
    protected $isInterim;

    /**
     * Licence
     *
     * @var \Olcs\Db\Entity\Licence
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Licence", inversedBy="operatingCentres")
     * @ORM\JoinColumn(name="licence_id", referencedColumnName="id", nullable=false)
     */
    protected $licence;

    /**
     * No of trailers possessed
     *
     * @var int
     *
     * @ORM\Column(type="smallint", name="no_of_trailers_possessed", nullable=true)
     */
    protected $noOfTrailersPossessed;

    /**
     * No of vehicles possessed
     *
     * @var int
     *
     * @ORM\Column(type="smallint", name="no_of_vehicles_possessed", nullable=true)
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
     * Set the licence
     *
     * @param \Olcs\Db\Entity\Licence $licence
     * @return LicenceOperatingCentre
     */
    public function setLicence($licence)
    {
        $this->licence = $licence;

        return $this;
    }

    /**
     * Get the licence
     *
     * @return \Olcs\Db\Entity\Licence
     */
    public function getLicence()
    {
        return $this->licence;
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

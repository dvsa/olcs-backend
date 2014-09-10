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
        Traits\S4ManyToOne,
        Traits\LastModifiedByManyToOne,
        Traits\CreatedByManyToOne,
        Traits\OperatingCentreManyToOne,
        Traits\LicenceManyToOne,
        Traits\AdPlacedField,
        Traits\AdPlacedIn70Field,
        Traits\AdPlacedDateField,
        Traits\PermissionField,
        Traits\NoOfTrailersRequiredField,
        Traits\NoOfVehiclesRequiredField,
        Traits\NoOfVehiclesPossessedField,
        Traits\NoOfTrailersPossessedField,
        Traits\ViAction1Field,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Sufficient parking
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="sufficient_parking", nullable=false)
     */
    protected $sufficientParking;

    /**
     * Added date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="added_date", nullable=true)
     */
    protected $addedDate;

    /**
     * Deleted date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="deleted_date", nullable=true)
     */
    protected $deletedDate;

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
     * Set the added date
     *
     * @param \DateTime $addedDate
     * @return LicenceOperatingCentre
     */
    public function setAddedDate($addedDate)
    {
        $this->addedDate = $addedDate;

        return $this;
    }

    /**
     * Get the added date
     *
     * @return \DateTime
     */
    public function getAddedDate()
    {
        return $this->addedDate;
    }


    /**
     * Set the deleted date
     *
     * @param \DateTime $deletedDate
     * @return LicenceOperatingCentre
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

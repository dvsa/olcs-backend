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
 *        @ORM\Index(name="IDX_E7ABF5329E191ED6", columns={"s4_id"}),
 *        @ORM\Index(name="IDX_E7ABF53265CF370E", columns={"last_modified_by"}),
 *        @ORM\Index(name="IDX_E7ABF532DE12AB56", columns={"created_by"}),
 *        @ORM\Index(name="IDX_E7ABF53235382CCB", columns={"operating_centre_id"}),
 *        @ORM\Index(name="IDX_E7ABF53226EF07C9", columns={"licence_id"})
 *    }
 * )
 */
class LicenceOperatingCentre implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\CreatedByManyToOne,
        Traits\S4ManyToOne,
        Traits\LastModifiedByManyToOne,
        Traits\OperatingCentreManyToOne,
        Traits\LicenceManyToOne,
        Traits\SufficientParkingField,
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

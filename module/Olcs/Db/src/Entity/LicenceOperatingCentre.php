<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * LicenceOperatingCentre Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
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
        Traits\DeletedDateFieldAlt1,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Sufficient parking
     *
     * @var boolean
     *
     * @ORM\Column(type="yesnonull", name="sufficient_parking", nullable=false)
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
     * Is interim
     *
     * @var boolean
     *
     * @ORM\Column(type="yesnonull", name="is_interim", nullable=true)
     */
    protected $isInterim;

    /**
     * Publication appropriate
     *
     * @var boolean
     *
     * @ORM\Column(type="yesnonull", name="publication_appropriate", nullable=true)
     */
    protected $publicationAppropriate;

    /**
     * Set the sufficient parking
     *
     * @param boolean $sufficientParking
     * @return \Olcs\Db\Entity\LicenceOperatingCentre
     */
    public function setSufficientParking($sufficientParking)
    {
        $this->sufficientParking = $sufficientParking;

        return $this;
    }

    /**
     * Get the sufficient parking
     *
     * @return boolean
     */
    public function getSufficientParking()
    {
        return $this->sufficientParking;
    }

    /**
     * Set the added date
     *
     * @param \DateTime $addedDate
     * @return \Olcs\Db\Entity\LicenceOperatingCentre
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
     * Set the is interim
     *
     * @param boolean $isInterim
     * @return \Olcs\Db\Entity\LicenceOperatingCentre
     */
    public function setIsInterim($isInterim)
    {
        $this->isInterim = $isInterim;

        return $this;
    }

    /**
     * Get the is interim
     *
     * @return boolean
     */
    public function getIsInterim()
    {
        return $this->isInterim;
    }

    /**
     * Set the publication appropriate
     *
     * @param boolean $publicationAppropriate
     * @return \Olcs\Db\Entity\LicenceOperatingCentre
     */
    public function setPublicationAppropriate($publicationAppropriate)
    {
        $this->publicationAppropriate = $publicationAppropriate;

        return $this;
    }

    /**
     * Get the publication appropriate
     *
     * @return boolean
     */
    public function getPublicationAppropriate()
    {
        return $this->publicationAppropriate;
    }
}

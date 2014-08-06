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
        Traits\S4ManyToOne,
        Traits\LastModifiedByManyToOne,
        Traits\CreatedByManyToOne,
        Traits\OperatingCentreManyToOne,
        Traits\ApplicationManyToOne,
        Traits\AdPlacedField,
        Traits\AdPlacedIn70Field,
        Traits\AdPlacedDateField,
        Traits\PermissionField,
        Traits\NoOfTrailersRequiredField,
        Traits\NoOfVehiclesRequiredField,
        Traits\NoOfVehiclesPossessedField,
        Traits\NoOfTrailersPossessedField,
        Traits\ViAction1Field,
        Traits\AddedDateField,
        Traits\CustomDeletedDateField,
        Traits\IsInterimField,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Action
     *
     * @var string
     *
     * @ORM\Column(type="string", name="action", length=1, nullable=false)
     */
    protected $action;

    /**
     * Publication appropriate
     *
     * @var boolean
     *
     * @ORM\Column(type="yesnonull", name="publication_appropriate", nullable=false)
     */
    protected $publicationAppropriate = 0;

    /**
     * Sufficient parking
     *
     * @var boolean
     *
     * @ORM\Column(type="yesnonull", name="sufficient_parking", nullable=false)
     */
    protected $sufficientParking = 0;

    /**
     * Set the action
     *
     * @param string $action
     * @return \Olcs\Db\Entity\ApplicationOperatingCentre
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
     * Set the publication appropriate
     *
     * @param boolean $publicationAppropriate
     * @return \Olcs\Db\Entity\ApplicationOperatingCentre
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

    /**
     * Set the sufficient parking
     *
     * @param boolean $sufficientParking
     * @return \Olcs\Db\Entity\ApplicationOperatingCentre
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
}

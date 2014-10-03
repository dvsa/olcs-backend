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
 *        @ORM\Index(name="IDX_E08171B0DE12AB56", columns={"created_by"}),
 *        @ORM\Index(name="IDX_E08171B09E191ED6", columns={"s4_id"}),
 *        @ORM\Index(name="IDX_E08171B065CF370E", columns={"last_modified_by"}),
 *        @ORM\Index(name="IDX_E08171B03E030ACD", columns={"application_id"}),
 *        @ORM\Index(name="IDX_E08171B035382CCB", columns={"operating_centre_id"})
 *    }
 * )
 */
class ApplicationOperatingCentre implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\CreatedByManyToOne,
        Traits\S4ManyToOne,
        Traits\LastModifiedByManyToOne,
        Traits\ApplicationManyToOne,
        Traits\OperatingCentreManyToOne,
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
     * @ORM\Column(type="string", name="action", length=1, nullable=true)
     */
    protected $action;

    /**
     * Publication appropriate
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="publication_appropriate", nullable=false)
     */
    protected $publicationAppropriate;

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
}

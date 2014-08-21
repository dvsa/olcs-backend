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
        Traits\OperatingCentreManyToOneAlt1,
        Traits\ApplicationManyToOneAlt1,
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
     * S4
     *
     * @var \Olcs\Db\Entity\S4
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\S4", fetch="LAZY")
     * @ORM\JoinColumn(name="s4_id", referencedColumnName="id", nullable=true)
     */
    protected $s4;

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
    protected $publicationAppropriate = 0;

    /**
     * Sufficient parking
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="sufficient_parking", nullable=false)
     */
    protected $sufficientParking = 0;

    /**
     * Set the s4
     *
     * @param \Olcs\Db\Entity\S4 $s4
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
     * @return \Olcs\Db\Entity\S4
     */
    public function getS4()
    {
        return $this->s4;
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
}

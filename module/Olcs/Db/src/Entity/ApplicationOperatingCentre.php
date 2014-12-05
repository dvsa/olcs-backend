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
        Traits\Action1Field,
        Traits\AdPlacedField,
        Traits\AdPlacedIn70Field,
        Traits\AdPlacedDateField,
        Traits\PermissionField,
        Traits\NoOfTrailersRequiredField,
        Traits\NoOfVehiclesRequiredField,
        Traits\ViAction1Field,
        Traits\CustomDeletedDateField,
        Traits\IsInterimField,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Application
     *
     * @var \Olcs\Db\Entity\Application
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Application", inversedBy="operatingCentres")
     * @ORM\JoinColumn(name="application_id", referencedColumnName="id", nullable=false)
     */
    protected $application;

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

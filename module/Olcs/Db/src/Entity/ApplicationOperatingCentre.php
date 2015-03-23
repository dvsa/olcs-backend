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
 *        @ORM\Index(name="ix_application_operating_centre_application_id", columns={"application_id"}),
 *        @ORM\Index(name="ix_application_operating_centre_operating_centre_id", columns={"operating_centre_id"}),
 *        @ORM\Index(name="ix_application_operating_centre_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_application_operating_centre_last_modified_by", columns={"last_modified_by"}),
 *        @ORM\Index(name="ix_application_operating_centre_s4_id", columns={"s4_id"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="uk_application_operating_centre_olbs_key", columns={"olbs_key"})
 *    }
 * )
 */
class ApplicationOperatingCentre implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\Action1Field,
        Traits\AdPlacedDateField,
        Traits\AdPlacedIn70Field,
        Traits\CreatedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\CustomDeletedDateField,
        Traits\IdIdentity,
        Traits\IsInterimField,
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
     * @ORM\Column(type="yesnonull", name="ad_placed", nullable=false)
     */
    protected $adPlaced;

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
     * @ORM\Column(type="yesno", name="publication_appropriate", nullable=false, options={"default": 0})
     */
    protected $publicationAppropriate = 0;

    /**
     * Sufficient parking
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="sufficient_parking", nullable=false, options={"default": 0})
     */
    protected $sufficientParking = 0;

    /**
     * Set the ad placed
     *
     * @param string $adPlaced
     * @return ApplicationOperatingCentre
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

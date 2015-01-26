<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * PreviousLicence Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="previous_licence",
 *    indexes={
 *        @ORM\Index(name="fk_previous_licence_application1_idx", columns={"application_id"}),
 *        @ORM\Index(name="fk_previous_licence_ref_data1_idx", columns={"previous_licence_type"}),
 *        @ORM\Index(name="fk_previous_licence_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_previous_licence_user2_idx", columns={"last_modified_by"})
 *    }
 * )
 */
class PreviousLicence implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\CreatedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\DisqualificationDateField,
        Traits\DisqualificationLength255Field,
        Traits\HolderName90Field,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CustomLastModifiedOnField,
        Traits\LicNo18Field,
        Traits\PreviousLicenceTypeManyToOne,
        Traits\PurchaseDateField,
        Traits\CustomVersionField;

    /**
     * Application
     *
     * @var \Olcs\Db\Entity\Application
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Application", inversedBy="previousLicences")
     * @ORM\JoinColumn(name="application_id", referencedColumnName="id", nullable=false)
     */
    protected $application;

    /**
     * Will surrender
     *
     * @var string
     *
     * @ORM\Column(type="yesnonull", name="will_surrender", nullable=true)
     */
    protected $willSurrender;

    /**
     * Set the application
     *
     * @param \Olcs\Db\Entity\Application $application
     * @return PreviousLicence
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
     * Set the will surrender
     *
     * @param string $willSurrender
     * @return PreviousLicence
     */
    public function setWillSurrender($willSurrender)
    {
        $this->willSurrender = $willSurrender;

        return $this;
    }

    /**
     * Get the will surrender
     *
     * @return string
     */
    public function getWillSurrender()
    {
        return $this->willSurrender;
    }
}

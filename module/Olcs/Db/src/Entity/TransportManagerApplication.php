<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * TransportManagerApplication Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @Gedmo\SoftDeleteable(fieldName="deletedDate", timeAware=true)
 * @ORM\Table(name="transport_manager_application",
 *    indexes={
 *        @ORM\Index(name="fk_transport_manager_application_transport_manager1_idx", columns={"transport_manager_id"}),
 *        @ORM\Index(name="fk_transport_manager_application_application1_idx", columns={"application_id"}),
 *        @ORM\Index(name="fk_transport_manager_application_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_transport_manager_application_user2_idx", columns={"last_modified_by"})
 *    }
 * )
 */
class TransportManagerApplication implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\Action1Field,
        Traits\CreatedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\CustomDeletedDateField,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CustomLastModifiedOnField,
        Traits\TransportManagerManyToOneAlt1,
        Traits\CustomVersionField;

    /**
     * Application
     *
     * @var \Olcs\Db\Entity\Application
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Application")
     * @ORM\JoinColumn(name="application_id", referencedColumnName="id", nullable=false)
     */
    protected $application;

    /**
     * Set the application
     *
     * @param \Olcs\Db\Entity\Application $application
     * @return TransportManagerApplication
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
}

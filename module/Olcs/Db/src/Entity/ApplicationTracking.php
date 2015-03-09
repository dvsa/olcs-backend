<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * ApplicationTracking Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="application_tracking",
 *    indexes={
 *        @ORM\Index(name="fk_application_tracking_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_application_tracking_user2_idx", columns={"last_modified_by"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="fk_application_tracking_application_id_udx", columns={"application_id"})
 *    }
 * )
 */
class ApplicationTracking implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\AddressesStatusField,
        Traits\BusinessDetailsStatusField,
        Traits\BusinessTypeStatusField,
        Traits\CommunityLicencesStatusField,
        Traits\ConditionsUndertakingsStatusField,
        Traits\ConvictionsPenaltiesStatusField,
        Traits\CreatedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\DiscsStatusField,
        Traits\FinancialEvidenceStatusField,
        Traits\FinancialHistoryStatusField,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CustomLastModifiedOnField,
        Traits\LicenceHistoryStatusField,
        Traits\OperatingCentresStatusField,
        Traits\PeopleStatusField,
        Traits\SafetyStatusField,
        Traits\TaxiPhvStatusField,
        Traits\TransportManagersStatusField,
        Traits\TypeOfLicenceStatusField,
        Traits\UndertakingsStatusField,
        Traits\VehiclesDeclarationsStatusField,
        Traits\VehiclesPsvStatusField,
        Traits\VehiclesStatusField,
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
     * @return ApplicationTracking
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

<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * LicenceVehicleFee Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="licence_vehicle_fee",
 *    indexes={
 *        @ORM\Index(name="ix_licence_vehicle_fee_fee_id", columns={"fee_id"}),
 *        @ORM\Index(name="ix_licence_vehicle_fee_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_licence_vehicle_fee_last_modified_by", columns={"last_modified_by"}),
 *        @ORM\Index(name="fk_licence_vehicle_fee_licence_vehicle_id_licence_vehicle_id", columns={"licence_vehicle_id"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="uk_licence_vehicle_fee_olbs_key", columns={"olbs_key"})
 *    }
 * )
 */
class LicenceVehicleFee implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\CreatedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\FeeManyToOne,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CustomLastModifiedOnField,
        Traits\OlbsKeyField,
        Traits\CustomVersionField;

    /**
     * Licence vehicle
     *
     * @var \Olcs\Db\Entity\LicenceVehicle
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\LicenceVehicle")
     * @ORM\JoinColumn(name="licence_vehicle_id", referencedColumnName="id", nullable=false)
     */
    protected $licenceVehicle;

    /**
     * Set the licence vehicle
     *
     * @param \Olcs\Db\Entity\LicenceVehicle $licenceVehicle
     * @return LicenceVehicleFee
     */
    public function setLicenceVehicle($licenceVehicle)
    {
        $this->licenceVehicle = $licenceVehicle;

        return $this;
    }

    /**
     * Get the licence vehicle
     *
     * @return \Olcs\Db\Entity\LicenceVehicle
     */
    public function getLicenceVehicle()
    {
        return $this->licenceVehicle;
    }
}

<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * Scan Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="scan",
 *    indexes={
 *        @ORM\Index(name="ix_scan_application_id", columns={"application_id"}),
 *        @ORM\Index(name="ix_scan_bus_reg_id", columns={"bus_reg_id"}),
 *        @ORM\Index(name="ix_scan_licence_id", columns={"licence_id"}),
 *        @ORM\Index(name="ix_scan_case_id", columns={"case_id"}),
 *        @ORM\Index(name="ix_scan_transport_manager_id", columns={"transport_manager_id"}),
 *        @ORM\Index(name="ix_scan_sub_category_id", columns={"sub_category_id"}),
 *        @ORM\Index(name="ix_scan_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_scan_last_modified_by", columns={"last_modified_by"}),
 *        @ORM\Index(name="ix_scan_category_id", columns={"category_id"}),
 *        @ORM\Index(name="ix_scan_irfo_organisation_id", columns={"irfo_organisation_id"})
 *    }
 * )
 */
class Scan implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\ApplicationManyToOne,
        Traits\BusRegManyToOneAlt1,
        Traits\CaseManyToOne,
        Traits\CategoryManyToOne,
        Traits\CreatedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\Description100Field,
        Traits\IdIdentity,
        Traits\IrfoOrganisationManyToOne,
        Traits\LastModifiedByManyToOne,
        Traits\CustomLastModifiedOnField,
        Traits\LicenceManyToOneAlt1,
        Traits\SubCategoryManyToOne,
        Traits\TransportManagerManyToOne,
        Traits\CustomVersionField;
}

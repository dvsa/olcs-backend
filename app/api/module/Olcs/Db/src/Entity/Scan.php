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
 *        @ORM\Index(name="fk_scan_application1_idx", columns={"application_id"}),
 *        @ORM\Index(name="fk_scan_irfo_organisation1_idx", columns={"irfo_organisation_id"}),
 *        @ORM\Index(name="fk_scan_bus_reg1_idx", columns={"bus_reg_id"}),
 *        @ORM\Index(name="fk_scan_licence1_idx", columns={"licence_id"}),
 *        @ORM\Index(name="fk_scan_cases1_idx", columns={"case_id"}),
 *        @ORM\Index(name="fk_scan_transport_manager1_idx", columns={"transport_manager_id"}),
 *        @ORM\Index(name="fk_scan_category1_idx", columns={"category_id"}),
 *        @ORM\Index(name="fk_scan_sub_category1_idx", columns={"sub_category_id"}),
 *        @ORM\Index(name="fk_scan_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_scan_user2_idx", columns={"last_modified_by"})
 *    }
 * )
 */
class Scan implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\ApplicationManyToOneAlt1,
        Traits\BusRegManyToOneAlt1,
        Traits\CaseManyToOneAlt1,
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

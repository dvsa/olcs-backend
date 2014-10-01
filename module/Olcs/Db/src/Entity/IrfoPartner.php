<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * IrfoPartner Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="irfo_partner",
 *    indexes={
 *        @ORM\Index(name="IDX_1702418465CF370E", columns={"last_modified_by"}),
 *        @ORM\Index(name="IDX_17024184DE12AB56", columns={"created_by"}),
 *        @ORM\Index(name="IDX_170241844425C407", columns={"irfo_psv_auth_id"}),
 *        @ORM\Index(name="IDX_170241849E6B1585", columns={"organisation_id"})
 *    }
 * )
 */
class IrfoPartner implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\CreatedByManyToOne,
        Traits\LastModifiedByManyToOne,
        Traits\IrfoPsvAuthManyToOne,
        Traits\OrganisationManyToOne,
        Traits\Name70Field,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;
}

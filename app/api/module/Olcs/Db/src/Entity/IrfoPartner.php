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
 *        @ORM\Index(name="fk_irfo_partner_organisation1_idx", columns={"organisation_id"}),
 *        @ORM\Index(name="fk_irfo_partner_irfo_psv_auth1_idx", columns={"irfo_psv_auth_id"}),
 *        @ORM\Index(name="fk_irfo_partner_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_irfo_partner_user2_idx", columns={"last_modified_by"})
 *    }
 * )
 */
class IrfoPartner implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CreatedByManyToOne,
        Traits\IrfoPsvAuthManyToOne,
        Traits\OrganisationManyToOne,
        Traits\Name70Field,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;
}

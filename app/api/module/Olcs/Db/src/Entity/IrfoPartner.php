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
 *        @ORM\Index(name="ix_irfo_partner_organisation_id", columns={"organisation_id"}),
 *        @ORM\Index(name="ix_irfo_partner_irfo_psv_auth_id", columns={"irfo_psv_auth_id"}),
 *        @ORM\Index(name="ix_irfo_partner_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_irfo_partner_last_modified_by", columns={"last_modified_by"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="uk_irfo_partner_olbs_key", columns={"olbs_key"})
 *    }
 * )
 */
class IrfoPartner implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\CreatedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\IdIdentity,
        Traits\IrfoPsvAuthManyToOne,
        Traits\LastModifiedByManyToOne,
        Traits\CustomLastModifiedOnField,
        Traits\Name70Field,
        Traits\OlbsKeyField,
        Traits\OrganisationManyToOneAlt1,
        Traits\CustomVersionField;
}

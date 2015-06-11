<?php

namespace Dvsa\Olcs\Api\Entity\Cases;

use Doctrine\ORM\Mapping as ORM;

/**
 * Conviction Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="conviction",
 *    indexes={
 *        @ORM\Index(name="ix_conviction_transport_manager_id", columns={"transport_manager_id"}),
 *        @ORM\Index(name="ix_conviction_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_conviction_last_modified_by", columns={"last_modified_by"}),
 *        @ORM\Index(name="ix_conviction_case_id", columns={"case_id"}),
 *        @ORM\Index(name="ix_conviction_defendant_type", columns={"defendant_type"}),
 *        @ORM\Index(name="ix_conviction_conviction_category", columns={"conviction_category"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="uk_conviction_olbs_key", columns={"olbs_key"})
 *    }
 * )
 */
class Conviction extends AbstractConviction
{
    const DEFENDANT_TYPE_DIRECTOR     = 'def_t_dir';
    const DEFENDANT_TYPE_DRIVER       = 'def_t_driver';
    const DEFENDANT_TYPE_ORGANISATION = 'def_t_op';
    const DEFENDANT_TYPE_OTHER        = 'def_t_other';
    const DEFENDANT_TYPE_OWNER        = 'def_t_owner';
    const DEFENDANT_TYPE_PARTNER      = 'def_t_part';
    const DEFENDANT_TYPE_TM           = 'def_t_tm';
}

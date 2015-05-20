<?php

namespace Dvsa\Olcs\Api\Entity\OtherLicence;

use Doctrine\ORM\Mapping as ORM;

/**
 * OtherLicence Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="other_licence",
 *    indexes={
 *        @ORM\Index(name="ix_other_licence_application_id", columns={"application_id"}),
 *        @ORM\Index(name="ix_other_licence_previous_licence_type", columns={"previous_licence_type"}),
 *        @ORM\Index(name="ix_other_licence_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_other_licence_last_modified_by", columns={"last_modified_by"}),
 *        @ORM\Index(name="ix_other_licence_transport_manager_id", columns={"transport_manager_id"}),
 *        @ORM\Index(name="ix_other_licence_transport_manager_application_id", columns={"transport_manager_application_id"}),
 *        @ORM\Index(name="fk_other_licence_transport_manager_licence1_idx", columns={"transport_manager_licence_id"}),
 *        @ORM\Index(name="fk_other_licence_ref_data1_idx", columns={"role"})
 *    }
 * )
 */
class OtherLicence extends AbstractOtherLicence
{
    const TYPE_CURRENT = 'prev_has_licence';
    const TYPE_APPLIED = 'prev_had_licence';
    const TYPE_REFUSED = 'prev_been_refused';
    const TYPE_REVOKED = 'prev_been_revoked';
    const TYPE_PUBLIC_INQUIRY = 'prev_been_at_pi';
    const TYPE_DISQUALIFIED = 'prev_been_disqualified_tc';
    const TYPE_HELD = 'prev_purchased_assets';
}

<?php

namespace Dvsa\Olcs\Api\Entity\Licence;

use Doctrine\ORM\Mapping as ORM;

/**
 * ContinuationDetail Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="continuation_detail",
 *    indexes={
 *        @ORM\Index(name="fk_continuation_detail_continuation1_idx", columns={"continuation_id"}),
 *        @ORM\Index(name="fk_continuation_detail_licence1_idx", columns={"licence_id"}),
 *        @ORM\Index(name="fk_continuation_detail_ref_data1_idx", columns={"status"}),
 *        @ORM\Index(name="fk_continuation_detail_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_continuation_detail_user2_idx", columns={"last_modified_by"}),
 *        @ORM\Index(name="ix_continuation_detail_checklist_document_id",
 *     columns={"checklist_document_id"}),
 *        @ORM\Index(name="ix_continuation_detail_received", columns={"received"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="olbs_key_UNIQUE", columns={"olbs_key"})
 *    }
 * )
 */
class ContinuationDetail extends AbstractContinuationDetail
{
    const STATUS_PREPARED     = 'con_det_sts_prepared';
    const STATUS_PRINTING     = 'con_det_sts_printing';
    const STATUS_PRINTED      = 'con_det_sts_printed';
    const STATUS_UNACCEPTABLE = 'con_det_sts_unacceptable';
    const STATUS_ACCEPTABLE   = 'con_det_sts_acceptable';
    const STATUS_COMPLETE     = 'con_det_sts_complete';
    const STATUS_ERROR        = 'con_det_sts_error';
}

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

}

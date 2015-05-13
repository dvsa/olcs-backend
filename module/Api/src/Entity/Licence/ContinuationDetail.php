<?php

namespace Dvsa\Olcs\Api\Entity\Licence;

use Doctrine\ORM\Mapping as ORM;

/**
 * ContinuationDetail Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="continuation_detail",
 *    indexes={
 *        @ORM\Index(name="ix_continuation_detail_continuation_id", columns={"continuation_id"}),
 *        @ORM\Index(name="ix_continuation_detail_licence_id", columns={"licence_id"}),
 *        @ORM\Index(name="ix_continuation_detail_status", columns={"status"}),
 *        @ORM\Index(name="ix_continuation_detail_received", columns={"received"}),
 *        @ORM\Index(name="ix_continuation_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_continuation_last_modified_by", columns={"last_modified_by"})
 *    }
 * )
 */
class ContinuationDetail extends AbstractContinuationDetail
{

}

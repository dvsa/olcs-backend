<?php

namespace Dvsa\Olcs\Api\Entity\Queue;

use Doctrine\ORM\Mapping as ORM;

/**
 * Queue Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="queue",
 *    indexes={
 *        @ORM\Index(name="ix_queue_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_queue_last_modified_by", columns={"last_modified_by"}),
 *        @ORM\Index(name="ix_queue_type", columns={"type"}),
 *        @ORM\Index(name="ix_queue_status", columns={"status"})
 *    }
 * )
 */
class Queue extends AbstractQueue
{
    // Message statuses
    const STATUS_QUEUED = 'que_sts_queued';
    const STATUS_PROCESSING = 'que_sts_processing';
    const STATUS_COMPLETE = 'que_sts_complete';
    const STATUS_FAILED = 'que_sts_failed';

    // Message types
    const TYPE_COMPANIES_HOUSE_INITIAL = 'que_typ_ch_initial';
    const TYPE_COMPANIES_HOUSE_COMPARE = 'que_typ_ch_compare';
    const TYPE_CONT_CHECKLIST_REMINDER_GENERATE_LETTER = 'que_typ_cont_check_rem_gen_let';
}

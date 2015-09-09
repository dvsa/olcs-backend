<?php

namespace Dvsa\Olcs\Api\Entity\Queue;

use Doctrine\ORM\Mapping as ORM;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;

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
    const TYPE_CPID_EXPORT_CSV = 'que_typ_cpid_export_csv';

    public function incrementAttempts()
    {
        $curr = $this->getAttempts();
        $this->setAttempts(++$curr);
        return $this;
    }

    public function __construct(RefData $messageType = null)
    {
        if (!is_null($messageType)) {
            $this->setType($messageType);
        }
    }

    public function validateQueue($type, $status)
    {
        $statuses = [
            self::STATUS_QUEUED,
            self::STATUS_PROCESSING,
            self::STATUS_COMPLETE,
            self::STATUS_FAILED
        ];
        $types = [
            self::TYPE_COMPANIES_HOUSE_INITIAL,
            self::TYPE_COMPANIES_HOUSE_COMPARE,
            self::TYPE_CONT_CHECKLIST_REMINDER_GENERATE_LETTER,
            self::TYPE_CPID_EXPORT_CSV
        ];
        if (!in_array($type, $types)) {
            throw new ValidationException(['error' => 'Unknown queue type']);
        }
        if (!in_array($status, $statuses)) {
            throw new ValidationException(['error' => 'Unknown queue status']);
        }
    }
}

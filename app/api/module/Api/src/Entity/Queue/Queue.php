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

    protected $statuses = [
        self::STATUS_QUEUED,
        self::STATUS_PROCESSING,
        self::STATUS_COMPLETE,
        self::STATUS_FAILED
    ];

    // Message types
    const TYPE_COMPANIES_HOUSE_INITIAL = 'que_typ_ch_initial';
    const TYPE_COMPANIES_HOUSE_COMPARE = 'que_typ_ch_compare';
    const TYPE_CONT_CHECKLIST_REMINDER_GENERATE_LETTER = 'que_typ_cont_check_rem_gen_let';
    const TYPE_CPID_EXPORT_CSV = 'que_typ_cpid_export_csv';
    const TYPE_CONT_CHECKLIST = 'que_typ_cont_checklist';
    const TYPE_TM_SNAPSHOT = 'que_typ_tm_snapshot';
    const TYPE_CPMS_REPORT_DOWNLOAD = 'que_typ_cpms_report_download';
    const TYPE_EBSR_REQUEST_MAP = 'que_typ_ebsr_request_map';
    const TYPE_EBSR_PACK = 'que_typ_ebsr_pack';
    const TYPE_EMAIL = 'que_typ_email';
    const TYPE_PRINT = 'que_typ_print';
    const TYPE_DISC_PRINTING = 'que_typ_disc_printing';
    const TYPE_CREATE_GOODS_VEHICLE_LIST = 'que_typ_create_gds_vehicle_list';
    const TYPE_CREATE_PSV_VEHICLE_LIST = 'que_typ_create_psv_vehicle_list';

    protected $types = [
        self::TYPE_COMPANIES_HOUSE_INITIAL,
        self::TYPE_COMPANIES_HOUSE_COMPARE,
        self::TYPE_CONT_CHECKLIST_REMINDER_GENERATE_LETTER,
        self::TYPE_CPID_EXPORT_CSV,
        self::TYPE_CONT_CHECKLIST,
        self::TYPE_TM_SNAPSHOT,
        self::TYPE_CPMS_REPORT_DOWNLOAD,
        self::TYPE_EBSR_REQUEST_MAP,
        self::TYPE_EBSR_PACK,
        self::TYPE_EMAIL,
        self::TYPE_PRINT,
        self::TYPE_DISC_PRINTING,
        self::TYPE_CREATE_GOODS_VEHICLE_LIST,
        self::TYPE_CREATE_PSV_VEHICLE_LIST
    ];

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

    /**
     * @param string $type
     * @param string $status
     * @param string $processAfterDate
     * @throws ValidationException
     */
    public function validateQueue($type, $status, $processAfterDate)
    {
        if (!in_array($type, $this->types)) {
            throw new ValidationException(['error' => 'Unknown queue type']);
        }

        if (!in_array($status, $this->statuses)) {
            throw new ValidationException(['error' => 'Unknown queue status']);
        }

        //using not empty as potentially we could end up with empty string instead of null
        if (!empty($processAfterDate)) {
            $processAfterDateTime = \DateTime::createFromFormat('Y-m-d H:i:s', $processAfterDate);

            if (!$processAfterDateTime instanceof \DateTime) {
                throw new ValidationException(['error' => 'Queue process after date is not valid']);
            }
        }
    }
}

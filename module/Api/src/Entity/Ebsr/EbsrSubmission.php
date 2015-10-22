<?php

namespace Dvsa\Olcs\Api\Entity\Ebsr;

use Doctrine\ORM\Mapping as ORM;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Api\Entity\Doc\Document;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation;

/**
 * EbsrSubmission Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="ebsr_submission",
 *    indexes={
 *        @ORM\Index(name="ix_ebsr_submission_document_id", columns={"document_id"}),
 *        @ORM\Index(name="ix_ebsr_submission_bus_reg_id", columns={"bus_reg_id"}),
 *        @ORM\Index(name="ix_ebsr_submission_ebsr_submission_status_id", columns={"ebsr_submission_status_id"}),
 *        @ORM\Index(name="ix_ebsr_submission_ebsr_submission_type_id", columns={"ebsr_submission_type_id"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="uk_ebsr_submission_olbs_key", columns={"olbs_key"})
 *    }
 * )
 */
class EbsrSubmission extends AbstractEbsrSubmission
{
    const VALIDATING_STATUS = 'ebsrs_validating';
    const VALIDATED_STATUS = 'ebsrs_validated';
    const PROCESSED_STATUS = 'ebsrs_processed';

    /**
     * @param Organisation $organisation
     * @param RefData $ebsrSubmissionStatus
     * @param RefData $ebsrSubmissionType
     * @param Document $document
     * @param \DateTime $submittedDate
     */
    public function __construct(
        Organisation $organisation,
        RefData $ebsrSubmissionStatus,
        RefData $ebsrSubmissionType,
        Document $document,
        \DateTime $submittedDate
    ) {
        $this->organisation = $organisation;
        $this->ebsrSubmissionStatus = $ebsrSubmissionStatus;
        $this->ebsrSubmissionType = $ebsrSubmissionType;
        $this->document = $document;
        $this->submittedDate = $submittedDate;
    }
}

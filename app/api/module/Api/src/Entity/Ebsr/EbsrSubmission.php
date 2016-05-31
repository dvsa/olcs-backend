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
    const UPLOADED_STATUS = 'ebsrs_uploaded';
    const SUBMITTING_STATUS = 'ebsrs_submitting';
    const SUBMITTED_STATUS = 'ebsrs_submitted';
    const VALIDATING_STATUS = 'ebsrs_validating';
    const PROCESSED_STATUS = 'ebsrs_processed';
    const PROCESSING_STATUS = 'ebsrs_processing';
    const FAILED_STATUS = 'ebsrs_failed';

    const DATA_REFRESH_SUBMISSION_TYPE = 'ebsrt_refresh';
    const NEW_SUBMISSION_TYPE = 'ebsrt_new';
    const UNKNOWN_SUBMISSION_TYPE = 'ebsrt_unknown';

    /**
     * @param Organisation $organisation
     * @param RefData $ebsrSubmissionStatus
     * @param RefData $ebsrSubmissionType
     * @param Document $document
     */
    public function __construct(
        Organisation $organisation,
        RefData $ebsrSubmissionStatus,
        RefData $ebsrSubmissionType,
        Document $document
    ) {
        $this->organisation = $organisation;
        $this->ebsrSubmissionStatus = $ebsrSubmissionStatus;
        $this->ebsrSubmissionType = $ebsrSubmissionType;
        $this->document = $document;
    }

    /**
     * @param RefData $ebsrSubmissionStatus
     * @param RefData $ebsrSubmissionType
     */
    public function submit(RefData $ebsrSubmissionStatus, RefData $ebsrSubmissionType)
    {
        $this->ebsrSubmissionStatus = $ebsrSubmissionStatus;
        $this->ebsrSubmissionType = $ebsrSubmissionType;
        $this->submittedDate = new \DateTime();
    }

    /**
     * @param RefData $ebsrSubmissionStatus
     */
    public function beginValidating(RefData $ebsrSubmissionStatus)
    {
        $this->ebsrSubmissionStatus = $ebsrSubmissionStatus;
        $this->validationStart = new \DateTime();
    }

    /**
     * @param RefData $ebsrSubmissionStatus
     * @param String $ebsrSubmissionResult this is a serialized array
     */
    public function finishValidating(RefData $ebsrSubmissionStatus, $ebsrSubmissionResult)
    {
        $this->ebsrSubmissionStatus = $ebsrSubmissionStatus;
        $this->ebsrSubmissionResult = $ebsrSubmissionResult;
        $this->validationEnd = new \DateTime();

        //if the submission hasn't failed (so far - this isn't yet a success) then also populate processStart timestamp
        if (!$this->isFailure()) {
            $this->processStart = $this->validationEnd;
        }
    }

    /**
     * @return bool
     */
    public function isFailure()
    {
        return $this->ebsrSubmissionStatus->getId() === self::FAILED_STATUS;
    }

    /**
     * @param RefData $ebsrSubmissionStatus
     */
    public function finishProcessing(RefData $ebsrSubmissionStatus)
    {
        $this->ebsrSubmissionStatus = $ebsrSubmissionStatus;
        $this->processEnd = new \DateTime();
    }

    /**
     * @return bool
     */
    public function isDataRefresh()
    {
        return $this->ebsrSubmissionType->getId() === self::DATA_REFRESH_SUBMISSION_TYPE;
    }
}

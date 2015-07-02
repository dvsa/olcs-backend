<?php

namespace Dvsa\Olcs\Api\Entity\Inspection;

use Doctrine\ORM\Mapping as ORM;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;

/**
 * InspectionRequest Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="inspection_request",
 *    indexes={
 *        @ORM\Index(name="ix_inspection_request_licence_id", columns={"licence_id"}),
 *        @ORM\Index(name="ix_inspection_request_application_id", columns={"application_id"}),
 *        @ORM\Index(name="ix_inspection_request_operating_centre_id", columns={"operating_centre_id"}),
 *        @ORM\Index(name="ix_inspection_request_task_id", columns={"task_id"}),
 *        @ORM\Index(name="ix_inspection_request_case_id", columns={"case_id"}),
 *        @ORM\Index(name="ix_inspection_request_report_type", columns={"report_type"}),
 *        @ORM\Index(name="ix_inspection_request_request_type", columns={"request_type"}),
 *        @ORM\Index(name="ix_inspection_request_result_type", columns={"result_type"}),
 *        @ORM\Index(name="ix_inspection_request_requestor_user_id", columns={"requestor_user_id"}),
 *        @ORM\Index(name="ix_inspection_request_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_inspection_request_last_modified_by", columns={"last_modified_by"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="uk_inspection_request_olbs_key", columns={"olbs_key"})
 *    }
 * )
 */
class InspectionRequest extends AbstractInspectionRequest
{
    const ERROR_FIELD_IS_REQUIRED = 'IR-FR-1';
    const ERROR_DUE_DATE = 'IR-DD-2';
    const ERROR_DUE_DATE_NOT_IN_RANGE = 'IR-DD-3';

    const REPORT_TYPE_MAINTENANCE_REQUEST = 'insp_rep_t_maint';
    const RESULT_TYPE_NEW = 'insp_res_t_new';
    const RESULT_TYPE_SATISFACTORY = 'insp_res_t_new_sat';
    const RESULT_TYPE_UNSATISFACTORY = 'insp_res_t_new_unsat';
    const REQUEST_TYPE_NEW_OP = 'insp_req_t_new_op';

    private $duePeriods = [3, 6, 9, 12];

    public function updateInspectionRequest(
        $requestType,
        $requestDate,
        $dueDate,
        $duePeriod,
        $resultType,
        $requestorNotes,
        $reportType,
        $application,
        $licence,
        $requestorUser,
        $operatingCentre = null,
        $inspectorName = null,
        $returnDate = null,
        $fromDate = null,
        $toDate = null,
        $vehiclesExaminedNo = null,
        $trailersExaminedNo = null,
        $inspectorNotes = null
    ) {
        $this->validateInspectionRequest($reportType, $requestDate, $dueDate, $duePeriod, $resultType, $requestorUser);

        $this->setRequestType($requestType);

        if (!$requestDate) {
            $this->setRequestDate(new \DateTime());
        }
        if (!$dueDate) {
            $this->setDueDate(
                (new \DateTime())->add(new \DateInterval('P' . $duePeriod . 'M'))
            );
        }
        $this->setResultType($resultType);
        $this->setRequestorNotes($requestorNotes);
        $this->setReportType($reportType);
        $this->setApplication($application);
        $this->setLicence($licence);
        $this->setRequestorUser($requestorUser);
        $this->setOperatingCentre($operatingCentre);
        $this->setInspectorName($inspectorName);
        if ($returnDate) {
            $this->setReturnDate(new \DateTime($returnDate));
        }
        if ($fromDate) {
            $this->setFromDate(new \DateTime($fromDate));
        }
        if ($toDate) {
            $this->setToDate(new \DateTime($toDate));
        }
        $this->setVehiclesExaminedNo($vehiclesExaminedNo);
        $this->setTrailersExaminedNo($trailersExaminedNo);
        $this->setInspectorNotes($inspectorNotes);
    }

    protected function validateInspectionRequest(
        $reportType,
        $requestDate,
        $dueDate,
        $duePeriod,
        $resultType,
        $requestorUser
    ) {
        $errors = [];
        if (!$reportType) {
            $errors[] = [
                'reportType' => [
                    self::ERROR_FIELD_IS_REQUIRED => 'Field is required'
                ]
            ];
        }
        if (!$resultType) {
            $errors[] = [
                'resultType' => [
                    self::ERROR_FIELD_IS_REQUIRED => 'Field is required'
                ]
            ];
        }
        if (!$requestorUser) {
            $errors[] = [
                'requestorUser' => [
                    self::ERROR_FIELD_IS_REQUIRED => 'Field is required'
                ]
            ];
        }
        if (!$dueDate && !$duePeriod) {
            $errors[]['dueDate'][self::ERROR_FIELD_IS_REQUIRED] = 'Field is required';
        }
        if (!$duePeriod && !($dueDate > $requestDate)) {
            $errors[]['dueDate'][self::ERROR_DUE_DATE] = 'Due date should be the same or after date requested';
        }
        if ($duePeriod && !in_array($duePeriod, $this->duePeriods)) {
            $errors[]['dueDate'][self::ERROR_DUE_DATE_NOT_IN_RANGE] = 'Due date not in range';
        }
        if (count($errors)) {
            throw new ValidationException($errors);
        }
    }
}

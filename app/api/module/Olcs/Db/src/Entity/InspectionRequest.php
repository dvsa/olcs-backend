<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * InspectionRequest Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="inspection_request",
 *    indexes={
 *        @ORM\Index(name="fk_inspection_request_licence1_idx", columns={"licence_id"}),
 *        @ORM\Index(name="fk_inspection_request_application1_idx", columns={"application_id"}),
 *        @ORM\Index(name="fk_inspection_request_operating_centre1_idx", columns={"operating_centre_id"}),
 *        @ORM\Index(name="fk_inspection_request_task1_idx", columns={"task_id"}),
 *        @ORM\Index(name="fk_inspection_request_cases1_idx", columns={"case_id"}),
 *        @ORM\Index(name="fk_inspection_request_ref_data1_idx", columns={"report_type"}),
 *        @ORM\Index(name="fk_inspection_request_ref_data2_idx", columns={"request_type"}),
 *        @ORM\Index(name="fk_inspection_request_ref_data3_idx", columns={"result_type"}),
 *        @ORM\Index(name="fk_inspection_request_user1_idx", columns={"requestor_user_id"}),
 *        @ORM\Index(name="fk_inspection_request_user2_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_inspection_request_user3_idx", columns={"last_modified_by"})
 *    }
 * )
 */
class InspectionRequest implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\CreatedByManyToOne,
        Traits\LastModifiedByManyToOne,
        Traits\ApplicationManyToOne,
        Traits\OperatingCentreManyToOne,
        Traits\TaskManyToOne,
        Traits\CaseManyToOne,
        Traits\LicenceManyToOne,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Result type
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData")
     * @ORM\JoinColumn(name="result_type", referencedColumnName="id")
     */
    protected $resultType;

    /**
     * Requestor user
     *
     * @var \Olcs\Db\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\User")
     * @ORM\JoinColumn(name="requestor_user_id", referencedColumnName="id")
     */
    protected $requestorUser;

    /**
     * Request type
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData")
     * @ORM\JoinColumn(name="request_type", referencedColumnName="id")
     */
    protected $requestType;

    /**
     * Report type
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData")
     * @ORM\JoinColumn(name="report_type", referencedColumnName="id")
     */
    protected $reportType;

    /**
     * Requestor notes
     *
     * @var string
     *
     * @ORM\Column(type="text", name="requestor_notes", nullable=true)
     */
    protected $requestorNotes;

    /**
     * Inspector notes
     *
     * @var string
     *
     * @ORM\Column(type="text", name="inspector_notes", nullable=true)
     */
    protected $inspectorNotes;

    /**
     * Due date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="due_date", nullable=true)
     */
    protected $dueDate;

    /**
     * From date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="from_date", nullable=true)
     */
    protected $fromDate;

    /**
     * To date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="to_date", nullable=true)
     */
    protected $toDate;

    /**
     * Request date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="request_date", nullable=true)
     */
    protected $requestDate;

    /**
     * Return date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="return_date", nullable=true)
     */
    protected $returnDate;

    /**
     * Deferred date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="deferred_date", nullable=true)
     */
    protected $deferredDate;

    /**
     * Inspector name
     *
     * @var string
     *
     * @ORM\Column(type="string", name="inspector_name", length=70, nullable=true)
     */
    protected $inspectorName;

    /**
     * Local services no
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="local_services_no", nullable=true)
     */
    protected $localServicesNo;

    /**
     * Trailors examined no
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="trailors_examined_no", nullable=true)
     */
    protected $trailorsExaminedNo;

    /**
     * Vehicles examined no
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="vehicles_examined_no", nullable=true)
     */
    protected $vehiclesExaminedNo;

    /**
     * Set the result type
     *
     * @param \Olcs\Db\Entity\RefData $resultType
     * @return \Olcs\Db\Entity\InspectionRequest
     */
    public function setResultType($resultType)
    {
        $this->resultType = $resultType;

        return $this;
    }

    /**
     * Get the result type
     *
     * @return \Olcs\Db\Entity\RefData
     */
    public function getResultType()
    {
        return $this->resultType;
    }

    /**
     * Set the requestor user
     *
     * @param \Olcs\Db\Entity\User $requestorUser
     * @return \Olcs\Db\Entity\InspectionRequest
     */
    public function setRequestorUser($requestorUser)
    {
        $this->requestorUser = $requestorUser;

        return $this;
    }

    /**
     * Get the requestor user
     *
     * @return \Olcs\Db\Entity\User
     */
    public function getRequestorUser()
    {
        return $this->requestorUser;
    }

    /**
     * Set the request type
     *
     * @param \Olcs\Db\Entity\RefData $requestType
     * @return \Olcs\Db\Entity\InspectionRequest
     */
    public function setRequestType($requestType)
    {
        $this->requestType = $requestType;

        return $this;
    }

    /**
     * Get the request type
     *
     * @return \Olcs\Db\Entity\RefData
     */
    public function getRequestType()
    {
        return $this->requestType;
    }

    /**
     * Set the report type
     *
     * @param \Olcs\Db\Entity\RefData $reportType
     * @return \Olcs\Db\Entity\InspectionRequest
     */
    public function setReportType($reportType)
    {
        $this->reportType = $reportType;

        return $this;
    }

    /**
     * Get the report type
     *
     * @return \Olcs\Db\Entity\RefData
     */
    public function getReportType()
    {
        return $this->reportType;
    }

    /**
     * Set the requestor notes
     *
     * @param string $requestorNotes
     * @return \Olcs\Db\Entity\InspectionRequest
     */
    public function setRequestorNotes($requestorNotes)
    {
        $this->requestorNotes = $requestorNotes;

        return $this;
    }

    /**
     * Get the requestor notes
     *
     * @return string
     */
    public function getRequestorNotes()
    {
        return $this->requestorNotes;
    }

    /**
     * Set the inspector notes
     *
     * @param string $inspectorNotes
     * @return \Olcs\Db\Entity\InspectionRequest
     */
    public function setInspectorNotes($inspectorNotes)
    {
        $this->inspectorNotes = $inspectorNotes;

        return $this;
    }

    /**
     * Get the inspector notes
     *
     * @return string
     */
    public function getInspectorNotes()
    {
        return $this->inspectorNotes;
    }

    /**
     * Set the due date
     *
     * @param \DateTime $dueDate
     * @return \Olcs\Db\Entity\InspectionRequest
     */
    public function setDueDate($dueDate)
    {
        $this->dueDate = $dueDate;

        return $this;
    }

    /**
     * Get the due date
     *
     * @return \DateTime
     */
    public function getDueDate()
    {
        return $this->dueDate;
    }

    /**
     * Set the from date
     *
     * @param \DateTime $fromDate
     * @return \Olcs\Db\Entity\InspectionRequest
     */
    public function setFromDate($fromDate)
    {
        $this->fromDate = $fromDate;

        return $this;
    }

    /**
     * Get the from date
     *
     * @return \DateTime
     */
    public function getFromDate()
    {
        return $this->fromDate;
    }

    /**
     * Set the to date
     *
     * @param \DateTime $toDate
     * @return \Olcs\Db\Entity\InspectionRequest
     */
    public function setToDate($toDate)
    {
        $this->toDate = $toDate;

        return $this;
    }

    /**
     * Get the to date
     *
     * @return \DateTime
     */
    public function getToDate()
    {
        return $this->toDate;
    }

    /**
     * Set the request date
     *
     * @param \DateTime $requestDate
     * @return \Olcs\Db\Entity\InspectionRequest
     */
    public function setRequestDate($requestDate)
    {
        $this->requestDate = $requestDate;

        return $this;
    }

    /**
     * Get the request date
     *
     * @return \DateTime
     */
    public function getRequestDate()
    {
        return $this->requestDate;
    }

    /**
     * Set the return date
     *
     * @param \DateTime $returnDate
     * @return \Olcs\Db\Entity\InspectionRequest
     */
    public function setReturnDate($returnDate)
    {
        $this->returnDate = $returnDate;

        return $this;
    }

    /**
     * Get the return date
     *
     * @return \DateTime
     */
    public function getReturnDate()
    {
        return $this->returnDate;
    }

    /**
     * Set the deferred date
     *
     * @param \DateTime $deferredDate
     * @return \Olcs\Db\Entity\InspectionRequest
     */
    public function setDeferredDate($deferredDate)
    {
        $this->deferredDate = $deferredDate;

        return $this;
    }

    /**
     * Get the deferred date
     *
     * @return \DateTime
     */
    public function getDeferredDate()
    {
        return $this->deferredDate;
    }

    /**
     * Set the inspector name
     *
     * @param string $inspectorName
     * @return \Olcs\Db\Entity\InspectionRequest
     */
    public function setInspectorName($inspectorName)
    {
        $this->inspectorName = $inspectorName;

        return $this;
    }

    /**
     * Get the inspector name
     *
     * @return string
     */
    public function getInspectorName()
    {
        return $this->inspectorName;
    }

    /**
     * Set the local services no
     *
     * @param int $localServicesNo
     * @return \Olcs\Db\Entity\InspectionRequest
     */
    public function setLocalServicesNo($localServicesNo)
    {
        $this->localServicesNo = $localServicesNo;

        return $this;
    }

    /**
     * Get the local services no
     *
     * @return int
     */
    public function getLocalServicesNo()
    {
        return $this->localServicesNo;
    }

    /**
     * Set the trailors examined no
     *
     * @param int $trailorsExaminedNo
     * @return \Olcs\Db\Entity\InspectionRequest
     */
    public function setTrailorsExaminedNo($trailorsExaminedNo)
    {
        $this->trailorsExaminedNo = $trailorsExaminedNo;

        return $this;
    }

    /**
     * Get the trailors examined no
     *
     * @return int
     */
    public function getTrailorsExaminedNo()
    {
        return $this->trailorsExaminedNo;
    }

    /**
     * Set the vehicles examined no
     *
     * @param int $vehiclesExaminedNo
     * @return \Olcs\Db\Entity\InspectionRequest
     */
    public function setVehiclesExaminedNo($vehiclesExaminedNo)
    {
        $this->vehiclesExaminedNo = $vehiclesExaminedNo;

        return $this;
    }

    /**
     * Get the vehicles examined no
     *
     * @return int
     */
    public function getVehiclesExaminedNo()
    {
        return $this->vehiclesExaminedNo;
    }
}

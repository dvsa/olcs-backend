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
        Traits\ApplicationManyToOneAlt1,
        Traits\CaseManyToOneAlt1,
        Traits\CreatedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CustomLastModifiedOnField,
        Traits\LicenceManyToOne,
        Traits\OperatingCentreManyToOne,
        Traits\TaskManyToOne,
        Traits\CustomVersionField;

    /**
     * Deferred date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="deferred_date", nullable=true)
     */
    protected $deferredDate;

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
     * Inspector name
     *
     * @var string
     *
     * @ORM\Column(type="string", name="inspector_name", length=70, nullable=true)
     */
    protected $inspectorName;

    /**
     * Inspector notes
     *
     * @var string
     *
     * @ORM\Column(type="text", name="inspector_notes", length=65535, nullable=true)
     */
    protected $inspectorNotes;

    /**
     * Local services no
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="local_services_no", nullable=true)
     */
    protected $localServicesNo;

    /**
     * Report type
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData")
     * @ORM\JoinColumn(name="report_type", referencedColumnName="id", nullable=true)
     */
    protected $reportType;

    /**
     * Request date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="request_date", nullable=true)
     */
    protected $requestDate;

    /**
     * Request type
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData")
     * @ORM\JoinColumn(name="request_type", referencedColumnName="id", nullable=false)
     */
    protected $requestType;

    /**
     * Requestor notes
     *
     * @var string
     *
     * @ORM\Column(type="text", name="requestor_notes", length=65535, nullable=true)
     */
    protected $requestorNotes;

    /**
     * Requestor user
     *
     * @var \Olcs\Db\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\User")
     * @ORM\JoinColumn(name="requestor_user_id", referencedColumnName="id", nullable=false)
     */
    protected $requestorUser;

    /**
     * Result type
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData")
     * @ORM\JoinColumn(name="result_type", referencedColumnName="id", nullable=false)
     */
    protected $resultType;

    /**
     * Return date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="return_date", nullable=true)
     */
    protected $returnDate;

    /**
     * To date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="to_date", nullable=true)
     */
    protected $toDate;

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
     * Set the deferred date
     *
     * @param \DateTime $deferredDate
     * @return InspectionRequest
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
     * Set the due date
     *
     * @param \DateTime $dueDate
     * @return InspectionRequest
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
     * @return InspectionRequest
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
     * Set the inspector name
     *
     * @param string $inspectorName
     * @return InspectionRequest
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
     * Set the inspector notes
     *
     * @param string $inspectorNotes
     * @return InspectionRequest
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
     * Set the local services no
     *
     * @param int $localServicesNo
     * @return InspectionRequest
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
     * Set the report type
     *
     * @param \Olcs\Db\Entity\RefData $reportType
     * @return InspectionRequest
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
     * Set the request date
     *
     * @param \DateTime $requestDate
     * @return InspectionRequest
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
     * Set the request type
     *
     * @param \Olcs\Db\Entity\RefData $requestType
     * @return InspectionRequest
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
     * Set the requestor notes
     *
     * @param string $requestorNotes
     * @return InspectionRequest
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
     * Set the requestor user
     *
     * @param \Olcs\Db\Entity\User $requestorUser
     * @return InspectionRequest
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
     * Set the result type
     *
     * @param \Olcs\Db\Entity\RefData $resultType
     * @return InspectionRequest
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
     * Set the return date
     *
     * @param \DateTime $returnDate
     * @return InspectionRequest
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
     * Set the to date
     *
     * @param \DateTime $toDate
     * @return InspectionRequest
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
     * Set the trailors examined no
     *
     * @param int $trailorsExaminedNo
     * @return InspectionRequest
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
     * @return InspectionRequest
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

<?php

namespace Dvsa\Olcs\Api\Entity\Ebsr;

use Doctrine\ORM\Mapping as ORM;

/**
 * EbsrSubmission Abstract Entity
 *
 * Auto-Generated
 *
 * @ORM\MappedSuperclass
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
abstract class AbstractEbsrSubmission
{

    /**
     * Application classification
     *
     * @var string
     *
     * @ORM\Column(type="string", name="application_classification", length=32, nullable=true)
     */
    protected $applicationClassification;

    /**
     * Bus reg
     *
     * @var \Dvsa\Olcs\Api\Entity\Bus\BusReg
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\Bus\BusReg", fetch="LAZY", inversedBy="ebsrSubmissions")
     * @ORM\JoinColumn(name="bus_reg_id", referencedColumnName="id", nullable=true)
     */
    protected $busReg;

    /**
     * Distribute end
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="distribute_end", nullable=true)
     */
    protected $distributeEnd;

    /**
     * Distribute expire
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="distribute_expire", nullable=true)
     */
    protected $distributeExpire;

    /**
     * Distribute start
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="distribute_start", nullable=true)
     */
    protected $distributeStart;

    /**
     * Document
     *
     * @var \Dvsa\Olcs\Api\Entity\Doc\Document
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\Doc\Document", fetch="LAZY")
     * @ORM\JoinColumn(name="document_id", referencedColumnName="id", nullable=true)
     */
    protected $document;

    /**
     * Ebsr submission result
     *
     * @var string
     *
     * @ORM\Column(type="string", name="ebsr_submission_result", length=64, nullable=true)
     */
    protected $ebsrSubmissionResult;

    /**
     * Ebsr submission status
     *
     * @var \Dvsa\Olcs\Api\Entity\System\RefData
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\System\RefData", fetch="LAZY")
     * @ORM\JoinColumn(name="ebsr_submission_status_id", referencedColumnName="id", nullable=false)
     */
    protected $ebsrSubmissionStatus;

    /**
     * Ebsr submission type
     *
     * @var \Dvsa\Olcs\Api\Entity\System\RefData
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\System\RefData", fetch="LAZY")
     * @ORM\JoinColumn(name="ebsr_submission_type_id", referencedColumnName="id", nullable=false)
     */
    protected $ebsrSubmissionType;

    /**
     * Identifier - Id
     *
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer", name="id")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * Is from ftp
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="is_from_ftp", nullable=false, options={"default": 0})
     */
    protected $isFromFtp = 0;

    /**
     * Licence no
     *
     * @var string
     *
     * @ORM\Column(type="string", name="licence_no", length=7, nullable=true)
     */
    protected $licenceNo;

    /**
     * Olbs key
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="olbs_key", nullable=true)
     */
    protected $olbsKey;

    /**
     * Organisation email address
     *
     * @var string
     *
     * @ORM\Column(type="string", name="organisation_email_address", length=100, nullable=true)
     */
    protected $organisationEmailAddress;

    /**
     * Organisation id
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="organisation_id", nullable=true)
     */
    protected $organisationId;

    /**
     * Process end
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="process_end", nullable=true)
     */
    protected $processEnd;

    /**
     * Process start
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="process_start", nullable=true)
     */
    protected $processStart;

    /**
     * Publish end
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="publish_end", nullable=true)
     */
    protected $publishEnd;

    /**
     * Publish start
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="publish_start", nullable=true)
     */
    protected $publishStart;

    /**
     * Registration no
     *
     * @var string
     *
     * @ORM\Column(type="string", name="registration_no", length=4, nullable=true)
     */
    protected $registrationNo;

    /**
     * Submitted date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="submitted_date", nullable=true)
     */
    protected $submittedDate;

    /**
     * Tan code
     *
     * @var string
     *
     * @ORM\Column(type="string", name="tan_code", length=2, nullable=true)
     */
    protected $tanCode;

    /**
     * Validation end
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="validation_end", nullable=true)
     */
    protected $validationEnd;

    /**
     * Validation start
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="validation_start", nullable=true)
     */
    protected $validationStart;

    /**
     * Variation no
     *
     * @var int
     *
     * @ORM\Column(type="smallint", name="variation_no", nullable=true)
     */
    protected $variationNo;

    /**
     * Version
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="version", nullable=false, options={"default": 1})
     * @ORM\Version
     */
    protected $version = 1;

    /**
     * Set the application classification
     *
     * @param string $applicationClassification
     * @return EbsrSubmission
     */
    public function setApplicationClassification($applicationClassification)
    {
        $this->applicationClassification = $applicationClassification;

        return $this;
    }

    /**
     * Get the application classification
     *
     * @return string
     */
    public function getApplicationClassification()
    {
        return $this->applicationClassification;
    }

    /**
     * Set the bus reg
     *
     * @param \Dvsa\Olcs\Api\Entity\Bus\BusReg $busReg
     * @return EbsrSubmission
     */
    public function setBusReg($busReg)
    {
        $this->busReg = $busReg;

        return $this;
    }

    /**
     * Get the bus reg
     *
     * @return \Dvsa\Olcs\Api\Entity\Bus\BusReg
     */
    public function getBusReg()
    {
        return $this->busReg;
    }

    /**
     * Set the distribute end
     *
     * @param \DateTime $distributeEnd
     * @return EbsrSubmission
     */
    public function setDistributeEnd($distributeEnd)
    {
        $this->distributeEnd = $distributeEnd;

        return $this;
    }

    /**
     * Get the distribute end
     *
     * @return \DateTime
     */
    public function getDistributeEnd()
    {
        return $this->distributeEnd;
    }

    /**
     * Set the distribute expire
     *
     * @param \DateTime $distributeExpire
     * @return EbsrSubmission
     */
    public function setDistributeExpire($distributeExpire)
    {
        $this->distributeExpire = $distributeExpire;

        return $this;
    }

    /**
     * Get the distribute expire
     *
     * @return \DateTime
     */
    public function getDistributeExpire()
    {
        return $this->distributeExpire;
    }

    /**
     * Set the distribute start
     *
     * @param \DateTime $distributeStart
     * @return EbsrSubmission
     */
    public function setDistributeStart($distributeStart)
    {
        $this->distributeStart = $distributeStart;

        return $this;
    }

    /**
     * Get the distribute start
     *
     * @return \DateTime
     */
    public function getDistributeStart()
    {
        return $this->distributeStart;
    }

    /**
     * Set the document
     *
     * @param \Dvsa\Olcs\Api\Entity\Doc\Document $document
     * @return EbsrSubmission
     */
    public function setDocument($document)
    {
        $this->document = $document;

        return $this;
    }

    /**
     * Get the document
     *
     * @return \Dvsa\Olcs\Api\Entity\Doc\Document
     */
    public function getDocument()
    {
        return $this->document;
    }

    /**
     * Set the ebsr submission result
     *
     * @param string $ebsrSubmissionResult
     * @return EbsrSubmission
     */
    public function setEbsrSubmissionResult($ebsrSubmissionResult)
    {
        $this->ebsrSubmissionResult = $ebsrSubmissionResult;

        return $this;
    }

    /**
     * Get the ebsr submission result
     *
     * @return string
     */
    public function getEbsrSubmissionResult()
    {
        return $this->ebsrSubmissionResult;
    }

    /**
     * Set the ebsr submission status
     *
     * @param \Dvsa\Olcs\Api\Entity\System\RefData $ebsrSubmissionStatus
     * @return EbsrSubmission
     */
    public function setEbsrSubmissionStatus($ebsrSubmissionStatus)
    {
        $this->ebsrSubmissionStatus = $ebsrSubmissionStatus;

        return $this;
    }

    /**
     * Get the ebsr submission status
     *
     * @return \Dvsa\Olcs\Api\Entity\System\RefData
     */
    public function getEbsrSubmissionStatus()
    {
        return $this->ebsrSubmissionStatus;
    }

    /**
     * Set the ebsr submission type
     *
     * @param \Dvsa\Olcs\Api\Entity\System\RefData $ebsrSubmissionType
     * @return EbsrSubmission
     */
    public function setEbsrSubmissionType($ebsrSubmissionType)
    {
        $this->ebsrSubmissionType = $ebsrSubmissionType;

        return $this;
    }

    /**
     * Get the ebsr submission type
     *
     * @return \Dvsa\Olcs\Api\Entity\System\RefData
     */
    public function getEbsrSubmissionType()
    {
        return $this->ebsrSubmissionType;
    }

    /**
     * Set the id
     *
     * @param int $id
     * @return EbsrSubmission
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the is from ftp
     *
     * @param string $isFromFtp
     * @return EbsrSubmission
     */
    public function setIsFromFtp($isFromFtp)
    {
        $this->isFromFtp = $isFromFtp;

        return $this;
    }

    /**
     * Get the is from ftp
     *
     * @return string
     */
    public function getIsFromFtp()
    {
        return $this->isFromFtp;
    }

    /**
     * Set the licence no
     *
     * @param string $licenceNo
     * @return EbsrSubmission
     */
    public function setLicenceNo($licenceNo)
    {
        $this->licenceNo = $licenceNo;

        return $this;
    }

    /**
     * Get the licence no
     *
     * @return string
     */
    public function getLicenceNo()
    {
        return $this->licenceNo;
    }

    /**
     * Set the olbs key
     *
     * @param int $olbsKey
     * @return EbsrSubmission
     */
    public function setOlbsKey($olbsKey)
    {
        $this->olbsKey = $olbsKey;

        return $this;
    }

    /**
     * Get the olbs key
     *
     * @return int
     */
    public function getOlbsKey()
    {
        return $this->olbsKey;
    }

    /**
     * Set the organisation email address
     *
     * @param string $organisationEmailAddress
     * @return EbsrSubmission
     */
    public function setOrganisationEmailAddress($organisationEmailAddress)
    {
        $this->organisationEmailAddress = $organisationEmailAddress;

        return $this;
    }

    /**
     * Get the organisation email address
     *
     * @return string
     */
    public function getOrganisationEmailAddress()
    {
        return $this->organisationEmailAddress;
    }

    /**
     * Set the organisation id
     *
     * @param int $organisationId
     * @return EbsrSubmission
     */
    public function setOrganisationId($organisationId)
    {
        $this->organisationId = $organisationId;

        return $this;
    }

    /**
     * Get the organisation id
     *
     * @return int
     */
    public function getOrganisationId()
    {
        return $this->organisationId;
    }

    /**
     * Set the process end
     *
     * @param \DateTime $processEnd
     * @return EbsrSubmission
     */
    public function setProcessEnd($processEnd)
    {
        $this->processEnd = $processEnd;

        return $this;
    }

    /**
     * Get the process end
     *
     * @return \DateTime
     */
    public function getProcessEnd()
    {
        return $this->processEnd;
    }

    /**
     * Set the process start
     *
     * @param \DateTime $processStart
     * @return EbsrSubmission
     */
    public function setProcessStart($processStart)
    {
        $this->processStart = $processStart;

        return $this;
    }

    /**
     * Get the process start
     *
     * @return \DateTime
     */
    public function getProcessStart()
    {
        return $this->processStart;
    }

    /**
     * Set the publish end
     *
     * @param \DateTime $publishEnd
     * @return EbsrSubmission
     */
    public function setPublishEnd($publishEnd)
    {
        $this->publishEnd = $publishEnd;

        return $this;
    }

    /**
     * Get the publish end
     *
     * @return \DateTime
     */
    public function getPublishEnd()
    {
        return $this->publishEnd;
    }

    /**
     * Set the publish start
     *
     * @param \DateTime $publishStart
     * @return EbsrSubmission
     */
    public function setPublishStart($publishStart)
    {
        $this->publishStart = $publishStart;

        return $this;
    }

    /**
     * Get the publish start
     *
     * @return \DateTime
     */
    public function getPublishStart()
    {
        return $this->publishStart;
    }

    /**
     * Set the registration no
     *
     * @param string $registrationNo
     * @return EbsrSubmission
     */
    public function setRegistrationNo($registrationNo)
    {
        $this->registrationNo = $registrationNo;

        return $this;
    }

    /**
     * Get the registration no
     *
     * @return string
     */
    public function getRegistrationNo()
    {
        return $this->registrationNo;
    }

    /**
     * Set the submitted date
     *
     * @param \DateTime $submittedDate
     * @return EbsrSubmission
     */
    public function setSubmittedDate($submittedDate)
    {
        $this->submittedDate = $submittedDate;

        return $this;
    }

    /**
     * Get the submitted date
     *
     * @return \DateTime
     */
    public function getSubmittedDate()
    {
        return $this->submittedDate;
    }

    /**
     * Set the tan code
     *
     * @param string $tanCode
     * @return EbsrSubmission
     */
    public function setTanCode($tanCode)
    {
        $this->tanCode = $tanCode;

        return $this;
    }

    /**
     * Get the tan code
     *
     * @return string
     */
    public function getTanCode()
    {
        return $this->tanCode;
    }

    /**
     * Set the validation end
     *
     * @param \DateTime $validationEnd
     * @return EbsrSubmission
     */
    public function setValidationEnd($validationEnd)
    {
        $this->validationEnd = $validationEnd;

        return $this;
    }

    /**
     * Get the validation end
     *
     * @return \DateTime
     */
    public function getValidationEnd()
    {
        return $this->validationEnd;
    }

    /**
     * Set the validation start
     *
     * @param \DateTime $validationStart
     * @return EbsrSubmission
     */
    public function setValidationStart($validationStart)
    {
        $this->validationStart = $validationStart;

        return $this;
    }

    /**
     * Get the validation start
     *
     * @return \DateTime
     */
    public function getValidationStart()
    {
        return $this->validationStart;
    }

    /**
     * Set the variation no
     *
     * @param int $variationNo
     * @return EbsrSubmission
     */
    public function setVariationNo($variationNo)
    {
        $this->variationNo = $variationNo;

        return $this;
    }

    /**
     * Get the variation no
     *
     * @return int
     */
    public function getVariationNo()
    {
        return $this->variationNo;
    }

    /**
     * Set the version
     *
     * @param int $version
     * @return EbsrSubmission
     */
    public function setVersion($version)
    {
        $this->version = $version;

        return $this;
    }

    /**
     * Get the version
     *
     * @return int
     */
    public function getVersion()
    {
        return $this->version;
    }



    /**
     * Clear properties
     *
     * @param type $properties
     */
    public function clearProperties($properties = array())
    {
        foreach ($properties as $property) {

            if (property_exists($this, $property)) {
                if ($this->$property instanceof Collection) {

                    $this->$property = new ArrayCollection(array());

                } else {

                    $this->$property = null;
                }
            }
        }
    }
}

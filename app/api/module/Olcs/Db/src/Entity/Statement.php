<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * Statement Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="statement",
 *    indexes={
 *        @ORM\Index(name="fk_statement_case1_idx", columns={"case_id"}),
 *        @ORM\Index(name="fk_statement_contact_details1_idx", columns={"requestors_contact_details_id"}),
 *        @ORM\Index(name="fk_statement_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_statement_user2_idx", columns={"last_modified_by"}),
 *        @ORM\Index(name="fk_statement_ref_data2_idx", columns={"contact_type"}),
 *        @ORM\Index(name="fk_statement_ref_data1_idx", columns={"statement_type"})
 *    }
 * )
 */
class Statement implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\CreatedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\IdIdentity,
        Traits\IssuedDateField,
        Traits\LastModifiedByManyToOne,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField,
        Traits\Vrm20Field;

    /**
     * Authorisers decision
     *
     * @var string
     *
     * @ORM\Column(type="string", name="authorisers_decision", length=4000, nullable=true)
     */
    protected $authorisersDecision;

    /**
     * Authorisers title
     *
     * @var string
     *
     * @ORM\Column(type="string", name="authorisers_title", length=40, nullable=true)
     */
    protected $authorisersTitle;

    /**
     * Case
     *
     * @var \Olcs\Db\Entity\Cases
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Cases", inversedBy="statements")
     * @ORM\JoinColumn(name="case_id", referencedColumnName="id", nullable=false)
     */
    protected $case;

    /**
     * Contact type
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData")
     * @ORM\JoinColumn(name="contact_type", referencedColumnName="id", nullable=true)
     */
    protected $contactType;

    /**
     * Licence no
     *
     * @var string
     *
     * @ORM\Column(type="string", name="licence_no", length=20, nullable=true)
     */
    protected $licenceNo;

    /**
     * Licence type
     *
     * @var string
     *
     * @ORM\Column(type="string", name="licence_type", length=32, nullable=true)
     */
    protected $licenceType;

    /**
     * Requested date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="requested_date", nullable=true)
     */
    protected $requestedDate;

    /**
     * Requestors body
     *
     * @var string
     *
     * @ORM\Column(type="string", name="requestors_body", length=40, nullable=true)
     */
    protected $requestorsBody;

    /**
     * Requestors contact details
     *
     * @var \Olcs\Db\Entity\ContactDetails
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\ContactDetails", cascade={"persist"})
     * @ORM\JoinColumn(name="requestors_contact_details_id", referencedColumnName="id", nullable=true)
     */
    protected $requestorsContactDetails;

    /**
     * Statement type
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData")
     * @ORM\JoinColumn(name="statement_type", referencedColumnName="id", nullable=false)
     */
    protected $statementType;

    /**
     * Stopped date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="stopped_date", nullable=true)
     */
    protected $stoppedDate;

    /**
     * Set the authorisers decision
     *
     * @param string $authorisersDecision
     * @return Statement
     */
    public function setAuthorisersDecision($authorisersDecision)
    {
        $this->authorisersDecision = $authorisersDecision;

        return $this;
    }

    /**
     * Get the authorisers decision
     *
     * @return string
     */
    public function getAuthorisersDecision()
    {
        return $this->authorisersDecision;
    }

    /**
     * Set the authorisers title
     *
     * @param string $authorisersTitle
     * @return Statement
     */
    public function setAuthorisersTitle($authorisersTitle)
    {
        $this->authorisersTitle = $authorisersTitle;

        return $this;
    }

    /**
     * Get the authorisers title
     *
     * @return string
     */
    public function getAuthorisersTitle()
    {
        return $this->authorisersTitle;
    }

    /**
     * Set the case
     *
     * @param \Olcs\Db\Entity\Cases $case
     * @return Statement
     */
    public function setCase($case)
    {
        $this->case = $case;

        return $this;
    }

    /**
     * Get the case
     *
     * @return \Olcs\Db\Entity\Cases
     */
    public function getCase()
    {
        return $this->case;
    }

    /**
     * Set the contact type
     *
     * @param \Olcs\Db\Entity\RefData $contactType
     * @return Statement
     */
    public function setContactType($contactType)
    {
        $this->contactType = $contactType;

        return $this;
    }

    /**
     * Get the contact type
     *
     * @return \Olcs\Db\Entity\RefData
     */
    public function getContactType()
    {
        return $this->contactType;
    }

    /**
     * Set the licence no
     *
     * @param string $licenceNo
     * @return Statement
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
     * Set the licence type
     *
     * @param string $licenceType
     * @return Statement
     */
    public function setLicenceType($licenceType)
    {
        $this->licenceType = $licenceType;

        return $this;
    }

    /**
     * Get the licence type
     *
     * @return string
     */
    public function getLicenceType()
    {
        return $this->licenceType;
    }

    /**
     * Set the requested date
     *
     * @param \DateTime $requestedDate
     * @return Statement
     */
    public function setRequestedDate($requestedDate)
    {
        $this->requestedDate = $requestedDate;

        return $this;
    }

    /**
     * Get the requested date
     *
     * @return \DateTime
     */
    public function getRequestedDate()
    {
        return $this->requestedDate;
    }

    /**
     * Set the requestors body
     *
     * @param string $requestorsBody
     * @return Statement
     */
    public function setRequestorsBody($requestorsBody)
    {
        $this->requestorsBody = $requestorsBody;

        return $this;
    }

    /**
     * Get the requestors body
     *
     * @return string
     */
    public function getRequestorsBody()
    {
        return $this->requestorsBody;
    }

    /**
     * Set the requestors contact details
     *
     * @param \Olcs\Db\Entity\ContactDetails $requestorsContactDetails
     * @return Statement
     */
    public function setRequestorsContactDetails($requestorsContactDetails)
    {
        $this->requestorsContactDetails = $requestorsContactDetails;

        return $this;
    }

    /**
     * Get the requestors contact details
     *
     * @return \Olcs\Db\Entity\ContactDetails
     */
    public function getRequestorsContactDetails()
    {
        return $this->requestorsContactDetails;
    }

    /**
     * Set the statement type
     *
     * @param \Olcs\Db\Entity\RefData $statementType
     * @return Statement
     */
    public function setStatementType($statementType)
    {
        $this->statementType = $statementType;

        return $this;
    }

    /**
     * Get the statement type
     *
     * @return \Olcs\Db\Entity\RefData
     */
    public function getStatementType()
    {
        return $this->statementType;
    }

    /**
     * Set the stopped date
     *
     * @param \DateTime $stoppedDate
     * @return Statement
     */
    public function setStoppedDate($stoppedDate)
    {
        $this->stoppedDate = $stoppedDate;

        return $this;
    }

    /**
     * Get the stopped date
     *
     * @return \DateTime
     */
    public function getStoppedDate()
    {
        return $this->stoppedDate;
    }
}

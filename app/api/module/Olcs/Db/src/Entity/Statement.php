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
 *        @ORM\Index(name="fk_statement_address1_idx", columns={"requesters_address_id"}),
 *        @ORM\Index(name="fk_statement_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_statement_user2_idx", columns={"last_modified_by"}),
 *        @ORM\Index(name="fk_statement_ref_data2_idx", columns={"contact_type"})
 *    }
 * )
 */
class Statement implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CreatedByManyToOne,
        Traits\CaseManyToOneAlt1,
        Traits\Vrm20Field,
        Traits\IssuedDateFieldAlt1,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Contact type
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData", fetch="LAZY")
     * @ORM\JoinColumn(name="contact_type", referencedColumnName="id", nullable=true)
     */
    protected $contactType;

    /**
     * Requesters address
     *
     * @var \Olcs\Db\Entity\Address
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Address", fetch="LAZY")
     * @ORM\JoinColumn(name="requesters_address_id", referencedColumnName="id", nullable=true)
     */
    protected $requestersAddress;

    /**
     * Statement type
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="statement_type", nullable=false)
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
     * Requested date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="requested_date", nullable=true)
     */
    protected $requestedDate;

    /**
     * Authorisers title
     *
     * @var string
     *
     * @ORM\Column(type="string", name="authorisers_title", length=40, nullable=true)
     */
    protected $authorisersTitle;

    /**
     * Authorisers decision
     *
     * @var string
     *
     * @ORM\Column(type="string", name="authorisers_decision", length=4000, nullable=true)
     */
    protected $authorisersDecision;

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
     * Requesters body
     *
     * @var string
     *
     * @ORM\Column(type="string", name="requesters_body", length=40, nullable=true)
     */
    protected $requestersBody;

    /**
     * Requesters family name
     *
     * @var string
     *
     * @ORM\Column(type="string", name="requesters_family_name", length=35, nullable=true)
     */
    protected $requestersFamilyName;

    /**
     * Requesters forename
     *
     * @var string
     *
     * @ORM\Column(type="string", name="requesters_forename", length=35, nullable=true)
     */
    protected $requestersForename;


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
     * Set the requesters address
     *
     * @param \Olcs\Db\Entity\Address $requestersAddress
     * @return Statement
     */
    public function setRequestersAddress($requestersAddress)
    {
        $this->requestersAddress = $requestersAddress;

        return $this;
    }

    /**
     * Get the requesters address
     *
     * @return \Olcs\Db\Entity\Address
     */
    public function getRequestersAddress()
    {
        return $this->requestersAddress;
    }

    /**
     * Set the statement type
     *
     * @param int $statementType
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
     * @return int
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
     * Set the requesters body
     *
     * @param string $requestersBody
     * @return Statement
     */
    public function setRequestersBody($requestersBody)
    {
        $this->requestersBody = $requestersBody;

        return $this;
    }

    /**
     * Get the requesters body
     *
     * @return string
     */
    public function getRequestersBody()
    {
        return $this->requestersBody;
    }

    /**
     * Set the requesters family name
     *
     * @param string $requestersFamilyName
     * @return Statement
     */
    public function setRequestersFamilyName($requestersFamilyName)
    {
        $this->requestersFamilyName = $requestersFamilyName;

        return $this;
    }

    /**
     * Get the requesters family name
     *
     * @return string
     */
    public function getRequestersFamilyName()
    {
        return $this->requestersFamilyName;
    }

    /**
     * Set the requesters forename
     *
     * @param string $requestersForename
     * @return Statement
     */
    public function setRequestersForename($requestersForename)
    {
        $this->requestersForename = $requestersForename;

        return $this;
    }

    /**
     * Get the requesters forename
     *
     * @return string
     */
    public function getRequestersForename()
    {
        return $this->requestersForename;
    }
}

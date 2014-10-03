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
 *        @ORM\Index(name="IDX_C0DB5176A421D5D6", columns={"contact_type"}),
 *        @ORM\Index(name="IDX_C0DB51769EFE5705", columns={"statement_type"}),
 *        @ORM\Index(name="IDX_C0DB517658B606A3", columns={"requestors_address_id"}),
 *        @ORM\Index(name="IDX_C0DB5176CF10D4F5", columns={"case_id"}),
 *        @ORM\Index(name="IDX_C0DB5176DE12AB56", columns={"created_by"}),
 *        @ORM\Index(name="IDX_C0DB517665CF370E", columns={"last_modified_by"})
 *    }
 * )
 */
class Statement implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\CaseManyToOneAlt1,
        Traits\CreatedByManyToOne,
        Traits\LastModifiedByManyToOne,
        Traits\Vrm20Field,
        Traits\IssuedDateField,
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
     * Statement type
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData", fetch="LAZY")
     * @ORM\JoinColumn(name="statement_type", referencedColumnName="id", nullable=false)
     */
    protected $statementType;

    /**
     * Requestors address
     *
     * @var \Olcs\Db\Entity\Address
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Address", fetch="LAZY")
     * @ORM\JoinColumn(name="requestors_address_id", referencedColumnName="id", nullable=true)
     */
    protected $requestorsAddress;

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
     * Requestors body
     *
     * @var string
     *
     * @ORM\Column(type="string", name="requestors_body", length=40, nullable=true)
     */
    protected $requestorsBody;

    /**
     * Requestors family name
     *
     * @var string
     *
     * @ORM\Column(type="string", name="requestors_family_name", length=35, nullable=true)
     */
    protected $requestorsFamilyName;

    /**
     * Requestors forename
     *
     * @var string
     *
     * @ORM\Column(type="string", name="requestors_forename", length=35, nullable=true)
     */
    protected $requestorsForename;

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
     * Set the requestors address
     *
     * @param \Olcs\Db\Entity\Address $requestorsAddress
     * @return Statement
     */
    public function setRequestorsAddress($requestorsAddress)
    {
        $this->requestorsAddress = $requestorsAddress;

        return $this;
    }

    /**
     * Get the requestors address
     *
     * @return \Olcs\Db\Entity\Address
     */
    public function getRequestorsAddress()
    {
        return $this->requestorsAddress;
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
     * Set the requestors family name
     *
     * @param string $requestorsFamilyName
     * @return Statement
     */
    public function setRequestorsFamilyName($requestorsFamilyName)
    {
        $this->requestorsFamilyName = $requestorsFamilyName;

        return $this;
    }

    /**
     * Get the requestors family name
     *
     * @return string
     */
    public function getRequestorsFamilyName()
    {
        return $this->requestorsFamilyName;
    }

    /**
     * Set the requestors forename
     *
     * @param string $requestorsForename
     * @return Statement
     */
    public function setRequestorsForename($requestorsForename)
    {
        $this->requestorsForename = $requestorsForename;

        return $this;
    }

    /**
     * Get the requestors forename
     *
     * @return string
     */
    public function getRequestorsForename()
    {
        return $this->requestorsForename;
    }
}

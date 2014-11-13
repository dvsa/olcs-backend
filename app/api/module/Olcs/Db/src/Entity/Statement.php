<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Statement Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="statement",
 *    indexes={
 *        @ORM\Index(name="fk_statement_case1_idx", 
 *            columns={"case_id"}),
 *        @ORM\Index(name="fk_statement_type1_idx", 
 *            columns={"statement_type"}),
 *        @ORM\Index(name="fk_statement_address1_idx", 
 *            columns={"requestors_address_id"}),
 *        @ORM\Index(name="fk_statement_user1_idx", 
 *            columns={"created_by"}),
 *        @ORM\Index(name="fk_statement_user2_idx", 
 *            columns={"last_modified_by"}),
 *        @ORM\Index(name="fk_statement_ref_data2_idx", 
 *            columns={"contact_type"})
 *    }
 * )
 */
class Statement implements Interfaces\EntityInterface
{

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
     * Contact type
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData", fetch="LAZY")
     * @ORM\JoinColumn(name="contact_type", referencedColumnName="id", nullable=true)
     */
    protected $contactType;

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
     * Created by
     *
     * @var \Olcs\Db\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\User", fetch="LAZY")
     * @ORM\JoinColumn(name="created_by", referencedColumnName="id", nullable=true)
     */
    protected $createdBy;

    /**
     * Last modified by
     *
     * @var \Olcs\Db\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\User", fetch="LAZY")
     * @ORM\JoinColumn(name="last_modified_by", referencedColumnName="id", nullable=true)
     */
    protected $lastModifiedBy;

    /**
     * Case
     *
     * @var \Olcs\Db\Entity\Cases
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Cases", fetch="LAZY")
     * @ORM\JoinColumn(name="case_id", referencedColumnName="id", nullable=false)
     */
    protected $case;

    /**
     * Vrm
     *
     * @var string
     *
     * @ORM\Column(type="string", name="vrm", length=20, nullable=true)
     */
    protected $vrm;

    /**
     * Issued date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="issued_date", nullable=true)
     */
    protected $issuedDate;

    /**
     * Created on
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="created_on", nullable=true)
     */
    protected $createdOn;

    /**
     * Last modified on
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="last_modified_on", nullable=true)
     */
    protected $lastModifiedOn;

    /**
     * Version
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="version", nullable=false)
     * @ORM\Version
     */
    protected $version;

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

    /**
     * Set the id
     *
     * @param int $id
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
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
     * Set the created by
     *
     * @param \Olcs\Db\Entity\User $createdBy
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get the created by
     *
     * @return \Olcs\Db\Entity\User
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Set the last modified by
     *
     * @param \Olcs\Db\Entity\User $lastModifiedBy
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setLastModifiedBy($lastModifiedBy)
    {
        $this->lastModifiedBy = $lastModifiedBy;

        return $this;
    }

    /**
     * Get the last modified by
     *
     * @return \Olcs\Db\Entity\User
     */
    public function getLastModifiedBy()
    {
        return $this->lastModifiedBy;
    }

    /**
     * Set the case
     *
     * @param \Olcs\Db\Entity\Cases $case
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
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
     * Set the vrm
     *
     * @param string $vrm
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setVrm($vrm)
    {
        $this->vrm = $vrm;

        return $this;
    }

    /**
     * Get the vrm
     *
     * @return string
     */
    public function getVrm()
    {
        return $this->vrm;
    }

    /**
     * Set the issued date
     *
     * @param \DateTime $issuedDate
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setIssuedDate($issuedDate)
    {
        $this->issuedDate = $issuedDate;

        return $this;
    }

    /**
     * Get the issued date
     *
     * @return \DateTime
     */
    public function getIssuedDate()
    {
        return $this->issuedDate;
    }

    /**
     * Set the created on
     *
     * @param \DateTime $createdOn
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setCreatedOn($createdOn)
    {
        $this->createdOn = $createdOn;

        return $this;
    }

    /**
     * Get the created on
     *
     * @return \DateTime
     */
    public function getCreatedOn()
    {
        return $this->createdOn;
    }

    /**
     * Set the createdOn field on persist
     *
     * @ORM\PrePersist
     */
    public function setCreatedOnBeforePersist()
    {
        $this->setCreatedOn(new \DateTime('NOW'));
    }

    /**
     * Set the last modified on
     *
     * @param \DateTime $lastModifiedOn
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setLastModifiedOn($lastModifiedOn)
    {
        $this->lastModifiedOn = $lastModifiedOn;

        return $this;
    }

    /**
     * Get the last modified on
     *
     * @return \DateTime
     */
    public function getLastModifiedOn()
    {
        return $this->lastModifiedOn;
    }

    /**
     * Set the lastModifiedOn field on persist
     *
     * @ORM\PreUpdate
     */
    public function setLastModifiedOnBeforeUpdate()
    {
        $this->setLastModifiedOn(new \DateTime('NOW'));
    }

    /**
     * Set the version
     *
     * @param int $version
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
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
     * Set the version field on persist
     *
     * @ORM\PrePersist
     */
    public function setVersionBeforePersist()
    {
        $this->setVersion(1);
    }
}

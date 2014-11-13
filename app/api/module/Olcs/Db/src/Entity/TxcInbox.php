<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TxcInbox Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="txc_inbox",
 *    indexes={
 *        @ORM\Index(name="fk_txc_inbox_bus_reg1_idx", 
 *            columns={"bus_reg_id"}),
 *        @ORM\Index(name="fk_txc_inbox_local_authority1_idx", 
 *            columns={"local_authority_id"}),
 *        @ORM\Index(name="fk_txc_inbox_organisation1_idx", 
 *            columns={"organisation_id"}),
 *        @ORM\Index(name="fk_txc_inbox_document1_idx", 
 *            columns={"zip_document_id"}),
 *        @ORM\Index(name="fk_txc_inbox_document2_idx", 
 *            columns={"route_document_id"}),
 *        @ORM\Index(name="fk_txc_inbox_document3_idx", 
 *            columns={"pdf_document_id"}),
 *        @ORM\Index(name="fk_txc_inbox_user1_idx", 
 *            columns={"created_by"}),
 *        @ORM\Index(name="fk_txc_inbox_user2_idx", 
 *            columns={"last_modified_by"})
 *    }
 * )
 */
class TxcInbox implements Interfaces\EntityInterface
{

    /**
     * Pdf document
     *
     * @var \Olcs\Db\Entity\Document
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Document", fetch="LAZY")
     * @ORM\JoinColumn(name="pdf_document_id", referencedColumnName="id", nullable=false)
     */
    protected $pdfDocument;

    /**
     * Zip document
     *
     * @var \Olcs\Db\Entity\Document
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Document", fetch="LAZY")
     * @ORM\JoinColumn(name="zip_document_id", referencedColumnName="id", nullable=false)
     */
    protected $zipDocument;

    /**
     * Route document
     *
     * @var \Olcs\Db\Entity\Document
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Document", fetch="LAZY")
     * @ORM\JoinColumn(name="route_document_id", referencedColumnName="id", nullable=false)
     */
    protected $routeDocument;

    /**
     * File read
     *
     * @var string
     *
     * @ORM\Column(type="yesnonull", name="file_read", nullable=true)
     */
    protected $fileRead;

    /**
     * Route seq
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="route_seq", nullable=false)
     */
    protected $routeSeq;

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
     * Organisation
     *
     * @var \Olcs\Db\Entity\Organisation
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Organisation", fetch="LAZY")
     * @ORM\JoinColumn(name="organisation_id", referencedColumnName="id", nullable=false)
     */
    protected $organisation;

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
     * Local authority
     *
     * @var \Olcs\Db\Entity\LocalAuthority
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\LocalAuthority", fetch="LAZY")
     * @ORM\JoinColumn(name="local_authority_id", referencedColumnName="id", nullable=true)
     */
    protected $localAuthority;

    /**
     * Bus reg
     *
     * @var \Olcs\Db\Entity\BusReg
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\BusReg", fetch="LAZY")
     * @ORM\JoinColumn(name="bus_reg_id", referencedColumnName="id", nullable=true)
     */
    protected $busReg;

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
     * Set the pdf document
     *
     * @param \Olcs\Db\Entity\Document $pdfDocument
     * @return TxcInbox
     */
    public function setPdfDocument($pdfDocument)
    {
        $this->pdfDocument = $pdfDocument;

        return $this;
    }

    /**
     * Get the pdf document
     *
     * @return \Olcs\Db\Entity\Document
     */
    public function getPdfDocument()
    {
        return $this->pdfDocument;
    }

    /**
     * Set the zip document
     *
     * @param \Olcs\Db\Entity\Document $zipDocument
     * @return TxcInbox
     */
    public function setZipDocument($zipDocument)
    {
        $this->zipDocument = $zipDocument;

        return $this;
    }

    /**
     * Get the zip document
     *
     * @return \Olcs\Db\Entity\Document
     */
    public function getZipDocument()
    {
        return $this->zipDocument;
    }

    /**
     * Set the route document
     *
     * @param \Olcs\Db\Entity\Document $routeDocument
     * @return TxcInbox
     */
    public function setRouteDocument($routeDocument)
    {
        $this->routeDocument = $routeDocument;

        return $this;
    }

    /**
     * Get the route document
     *
     * @return \Olcs\Db\Entity\Document
     */
    public function getRouteDocument()
    {
        return $this->routeDocument;
    }

    /**
     * Set the file read
     *
     * @param string $fileRead
     * @return TxcInbox
     */
    public function setFileRead($fileRead)
    {
        $this->fileRead = $fileRead;

        return $this;
    }

    /**
     * Get the file read
     *
     * @return string
     */
    public function getFileRead()
    {
        return $this->fileRead;
    }

    /**
     * Set the route seq
     *
     * @param int $routeSeq
     * @return TxcInbox
     */
    public function setRouteSeq($routeSeq)
    {
        $this->routeSeq = $routeSeq;

        return $this;
    }

    /**
     * Get the route seq
     *
     * @return int
     */
    public function getRouteSeq()
    {
        return $this->routeSeq;
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
     * Set the organisation
     *
     * @param \Olcs\Db\Entity\Organisation $organisation
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setOrganisation($organisation)
    {
        $this->organisation = $organisation;

        return $this;
    }

    /**
     * Get the organisation
     *
     * @return \Olcs\Db\Entity\Organisation
     */
    public function getOrganisation()
    {
        return $this->organisation;
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
     * Set the local authority
     *
     * @param \Olcs\Db\Entity\LocalAuthority $localAuthority
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setLocalAuthority($localAuthority)
    {
        $this->localAuthority = $localAuthority;

        return $this;
    }

    /**
     * Get the local authority
     *
     * @return \Olcs\Db\Entity\LocalAuthority
     */
    public function getLocalAuthority()
    {
        return $this->localAuthority;
    }

    /**
     * Set the bus reg
     *
     * @param \Olcs\Db\Entity\BusReg $busReg
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setBusReg($busReg)
    {
        $this->busReg = $busReg;

        return $this;
    }

    /**
     * Get the bus reg
     *
     * @return \Olcs\Db\Entity\BusReg
     */
    public function getBusReg()
    {
        return $this->busReg;
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

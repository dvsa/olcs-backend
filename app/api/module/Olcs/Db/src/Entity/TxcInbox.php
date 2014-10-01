<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * TxcInbox Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="txc_inbox",
 *    indexes={
 *        @ORM\Index(name="IDX_CB46E42CCBFD05C", columns={"pdf_document_id"}),
 *        @ORM\Index(name="IDX_CB46E42CFD5F7826", columns={"route_document_id"}),
 *        @ORM\Index(name="IDX_CB46E42CC8A8A416", columns={"zip_document_id"}),
 *        @ORM\Index(name="IDX_CB46E42CDE12AB56", columns={"created_by"}),
 *        @ORM\Index(name="IDX_CB46E42C65CF370E", columns={"last_modified_by"}),
 *        @ORM\Index(name="IDX_CB46E42CB0E901C6", columns={"local_authority_id"}),
 *        @ORM\Index(name="IDX_CB46E42C9E6B1585", columns={"organisation_id"}),
 *        @ORM\Index(name="IDX_CB46E42C5327B2E3", columns={"bus_reg_id"})
 *    }
 * )
 */
class TxcInbox implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\CreatedByManyToOne,
        Traits\LocalAuthorityManyToOne,
        Traits\LastModifiedByManyToOne,
        Traits\OrganisationManyToOne,
        Traits\BusRegManyToOne,
        Traits\RouteSeqField,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

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
     * Route document
     *
     * @var \Olcs\Db\Entity\Document
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Document", fetch="LAZY")
     * @ORM\JoinColumn(name="route_document_id", referencedColumnName="id", nullable=false)
     */
    protected $routeDocument;

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
     * File read
     *
     * @var string
     *
     * @ORM\Column(type="yesnonull", name="file_read", nullable=true)
     */
    protected $fileRead;

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
}

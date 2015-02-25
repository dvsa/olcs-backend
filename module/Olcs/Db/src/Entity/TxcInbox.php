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
 *        @ORM\Index(name="fk_txc_inbox_bus_reg1_idx", columns={"bus_reg_id"}),
 *        @ORM\Index(name="fk_txc_inbox_local_authority1_idx", columns={"local_authority_id"}),
 *        @ORM\Index(name="fk_txc_inbox_organisation1_idx", columns={"organisation_id"}),
 *        @ORM\Index(name="fk_txc_inbox_document1_idx", columns={"zip_document_id"}),
 *        @ORM\Index(name="fk_txc_inbox_document2_idx", columns={"route_document_id"}),
 *        @ORM\Index(name="fk_txc_inbox_document3_idx", columns={"pdf_document_id"}),
 *        @ORM\Index(name="fk_txc_inbox_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_txc_inbox_user2_idx", columns={"last_modified_by"})
 *    }
 * )
 */
class TxcInbox implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\BusRegManyToOne,
        Traits\CreatedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CustomLastModifiedOnField,
        Traits\LocalAuthorityManyToOne,
        Traits\OrganisationManyToOneAlt1,
        Traits\CustomVersionField;

    /**
     * File read
     *
     * @var string
     *
     * @ORM\Column(type="yesnonull", name="file_read", nullable=true)
     */
    protected $fileRead;

    /**
     * Pdf document
     *
     * @var \Olcs\Db\Entity\Document
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Document")
     * @ORM\JoinColumn(name="pdf_document_id", referencedColumnName="id", nullable=false)
     */
    protected $pdfDocument;

    /**
     * Route document
     *
     * @var \Olcs\Db\Entity\Document
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Document")
     * @ORM\JoinColumn(name="route_document_id", referencedColumnName="id", nullable=false)
     */
    protected $routeDocument;

    /**
     * Variation no
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="variation_no", nullable=false)
     */
    protected $variationNo;

    /**
     * Zip document
     *
     * @var \Olcs\Db\Entity\Document
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Document")
     * @ORM\JoinColumn(name="zip_document_id", referencedColumnName="id", nullable=false)
     */
    protected $zipDocument;

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
     * Set the variation no
     *
     * @param int $variationNo
     * @return TxcInbox
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
}

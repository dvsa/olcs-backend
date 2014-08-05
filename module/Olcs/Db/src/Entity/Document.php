<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Olcs\Db\Entity\Traits;

/**
 * Document Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="document",
 *    indexes={
 *        @ORM\Index(name="fk_document_traffic_area1_idx", columns={"traffic_area_id"}),
 *        @ORM\Index(name="fk_document_document_category1_idx", columns={"category_id"}),
 *        @ORM\Index(name="fk_document_document_sub_category1_idx", columns={"document_sub_category_id"}),
 *        @ORM\Index(name="fk_document_licence1_idx", columns={"licence_id"}),
 *        @ORM\Index(name="fk_document_application1_idx", columns={"application_id"}),
 *        @ORM\Index(name="fk_document_cases1_idx", columns={"case_id"}),
 *        @ORM\Index(name="fk_document_transport_manager1_idx", columns={"transport_manager_id"}),
 *        @ORM\Index(name="fk_document_operating_centre1_idx", columns={"operating_centre_id"}),
 *        @ORM\Index(name="fk_document_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_document_user2_idx", columns={"last_modified_by"}),
 *        @ORM\Index(name="fk_document_opposition1_idx", columns={"opposition_id"}),
 *        @ORM\Index(name="fk_document_bus_reg1_idx", columns={"bus_reg_id"})
 *    }
 * )
 */
class Document implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\CreatedByManyToOne,
        Traits\OperatingCentreManyToOne,
        Traits\LastModifiedByManyToOne,
        Traits\OppositionManyToOne,
        Traits\BusRegManyToOne,
        Traits\TrafficAreaManyToOne,
        Traits\TransportManagerManyToOne,
        Traits\CategoryManyToOne,
        Traits\DocumentSubCategoryManyToOne,
        Traits\LicenceManyToOne,
        Traits\CaseManyToOne,
        Traits\ApplicationManyToOne,
        Traits\Description255FieldAlt1,
        Traits\IssuedDateField,
        Traits\DeletedDateField,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Email
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Olcs\Db\Entity\Email", mappedBy="documents")
     */
    protected $emails;

    /**
     * Document store id
     *
     * @var string
     *
     * @ORM\Column(type="string", name="document_store_id", length=255, nullable=false)
     */
    protected $documentStoreId;

    /**
     * Is read only
     *
     * @var boolean
     *
     * @ORM\Column(type="yesnonull", name="is_read_only", nullable=true)
     */
    protected $isReadOnly;

    /**
     * Filename
     *
     * @var string
     *
     * @ORM\Column(type="string", name="filename", length=255, nullable=true)
     */
    protected $filename;

    /**
     * Initialise the collections
     */
    public function __construct()
    {
        $this->emails = new ArrayCollection();
    }

    /**
     * Set the email
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $emails

     * @return \Olcs\Db\Entity\Document
     */
    public function setEmails($emails)
    {
        $this->emails = $emails;

        return $this;
    }

    /**
     * Get the email
     *
     * @return \Doctrine\Common\Collections\ArrayCollection

     */
    public function getEmails()
    {
        return $this->emails;
    }

    /**
     * Set the document store id
     *
     * @param string $documentStoreId
     * @return \Olcs\Db\Entity\Document
     */
    public function setDocumentStoreId($documentStoreId)
    {
        $this->documentStoreId = $documentStoreId;

        return $this;
    }

    /**
     * Get the document store id
     *
     * @return string
     */
    public function getDocumentStoreId()
    {
        return $this->documentStoreId;
    }

    /**
     * Set the is read only
     *
     * @param boolean $isReadOnly
     * @return \Olcs\Db\Entity\Document
     */
    public function setIsReadOnly($isReadOnly)
    {
        $this->isReadOnly = $isReadOnly;

        return $this;
    }

    /**
     * Get the is read only
     *
     * @return boolean
     */
    public function getIsReadOnly()
    {
        return $this->isReadOnly;
    }

    /**
     * Set the filename
     *
     * @param string $filename
     * @return \Olcs\Db\Entity\Document
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * Get the filename
     *
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }
}

<?php

/**
 * Document Search View
 *
 * @author Jessica Rowbottom <jess.rowbottom@valtech.co.uk>
 * @author Dan Eggleston <dan@stolenegg.com>
 */

namespace Olcs\Db\Entity\View;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Interfaces;

/**
 * Document Search View
 *
 * @ORM\Entity(readOnly=true)
 * @ORM\Table(name="document_search_view")
 */
class DocumentSearchView implements Interfaces\EntityInterface
{
    /**
     * Id
     *
     * @var int
     *
     * NOTE: The ID annotation here is to allow doctrine to create the table (Even though we remove it later)
     * @ORM\Id
     * @ORM\Column(type="integer", name="id")
     */
    protected $id;

    /**
     * Issued date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="issued_date")
     */
    protected $issuedDate;

    /**
     * Category ID
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="category_id")
     */
    protected $category;

    /**
     * Sub Category ID
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="sub_category_id")
     */
    protected $documentSubCategory;

    /**
     * Description
     *
     * @var string
     *
     * @ORM\Column(type="string", name="description")
     */
    protected $description;

    /**
     * Document Store Identifier
     *
     * @var string
     *
     * @ORM\Column(type="string", name="document_store_id")
     */
    protected $documentStoreIdentifier;

    /**
     * Document ID
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="document_id")
     */
    protected $document;

    /**
     * Category Name
     *
     * @var string
     *
     * @ORM\Column(type="string", name="category_name")
     */
    protected $categoryName;

    /**
     * Sub Category Name
     *
     * @var string
     *
     * @ORM\Column(type="string", name="document_sub_category_name")
     */
    protected $documentSubCategoryName;

    /**
     * Filename
     *
     * @var string
     *
     * @ORM\Column(type="string", name="filename")
     */
    protected $filename;

    /**
     * File Extension
     *
     * @var string
     *
     * @ORM\Column(type="string", name="file_extension")
     */
    protected $fileExtension;

    /**
     * Is digital or not
     *
     * @var bool
     *
     * @ORM\Column(type="boolean", name="is_digital")
     */
    protected $isDigital;

    /**
     * Document type
     *
     * @var string
     *
     * @ORM\Column(type="string", name="document_type")
     */
    protected $documentType;

    /**
     * Identifier
     *
     * @var string
     *
     * @ORM\Column(type="string", name="id_col")
     */
    protected $identifier;

    /**
     * Licence number
     *
     * @var string
     *
     * @ORM\Column(type="string", name="lic_no")
     */
    protected $licenceNo;

    /**
     * Licence ID
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="licence_id")
     */
    protected $licenceId;

    /**
     * Family name
     *
     * @var string
     *
     * @ORM\Column(type="string", name="family_name")
     */
    protected $familyName;

    /**
     * Case ID
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="case_id")
     */
    protected $caseId;

    /**
     * Bus Registration ID
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="bus_reg_id")
     */
    protected $busRegId;

    /**
     * Transport Manager ID
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="tm_id")
     */
    protected $tmId;

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
     * Get the action date
     *
     * @return \DateTime
     */
    public function getIssuedDate()
    {
        return $this->issuedDate;
    }

    /**
     * Get the category ID
     *
     * @return int
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Get the sub category ID
     *
     * @return int
     */
    public function getDocumentSubCategory()
    {
        return $this->documentSubCategory;
    }

    /**
     * Get the description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Get the document store identifier
     *
     * @return string
     */
    public function getDocumentStoreIdentifier()
    {
        return $this->documentStoreIdentifier;
    }

    /**
     * Get the document ID
     *
     * @return int
     */
    public function getDocument()
    {
        return $this->document;
    }

    /**
     * Get the category name
     *
     * @return string
     */
    public function getCategoryName()
    {
        return $this->categoryName;
    }

    /**
     * Get the sub category name
     *
     * @return string
     */
    public function getDocumentSubCategoryName()
    {
        return $this->documentSubCategoryName;
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

    /**
     * Get the file extension
     *
     * @return string
     */
    public function getFileExtension()
    {
        return $this->fileExtension;
    }

    /**
     * Get if digital
     *
     * @return int
     */
    public function getIsDigital()
    {
        return $this->isDigital;
    }

    /**
     * Get the document file type
     *
     * @return string
     */
    public function getDocumentType()
    {
        return $this->documentType;
    }

    /**
     * Get the identifier
     *
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * Get the licence number
     *
     * @return int
     */
    public function getLicenceNo()
    {
        return $this->licenceNo;
    }

    /**
     * Get the licence ID (if applicable)
     *
     * @return int
     */
    public function getLicenceId()
    {
        return $this->licenceId;
    }

    /**
     * Get the family name
     *
     * @return string
     */
    public function getFamilyName()
    {
        return $this->familyName;
    }

    /**
     * Get the case ID (if applicable)
     *
     * @return int
     */
    public function getCaseId()
    {
        return $this->caseId;
    }

    /**
     * Get the Bus Registration ID (if applicable)
     *
     * @return int
     */
    public function getBusRegId()
    {
        return $this->busRegId;
    }

    /**
     * Get the Transport Manager ID (if applicable)
     *
     * @return int
     */
    public function getTmId()
    {
        return $this->tmId;
    }
}

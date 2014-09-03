<?php

/**
 * Document Search View
 *
 * @author Jessica Rowbottom <jess.rowbottom@valtech.co.uk>
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
     * @ORM\Column(type="date", name="issued_date")
     */
    protected $issuedDate;

    /**
     * Identifier
     *
     * @var string
     *
     * @ORM\Column(type="string", name="id_col")
     */
    protected $identifier;

    /**
     * Description
     *
     * @var string
     *
     * @ORM\Column(type="string", name="description")
     */
    protected $description;

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
     * File Extension
     *
     * @var string
     *
     * @ORM\Column(type="string", name="document_type")
     */
    protected $documentType;

    /**
     * Category ID
     *
     * @var string
     *
     * @ORM\Column(type="integer", name="category_id")
     */
    protected $category;

    /**
     * Category Name
     *
     * @var string
     *
     * @ORM\Column(type="string", name="category_name")
     */
    protected $categoryName;

    /**
     * Sub Category ID
     *
     * @var string
     *
     * @ORM\Column(type="string", name="document_sub_category_id")
     */
    protected $documentSubCategory;

    /**
     * Sub Category Name
     *
     * @var string
     *
     * @ORM\Column(type="string", name="document_sub_category_name")
     */
    protected $documentSubCategoryName;

    /**
     * Is digital or not
     *
     * @var bool
     *
     * @ORM\Column(type="boolean", name="is_digital")
     */
    protected $isDigital;

    /**
     * Licence ID
     *
     * @var string
     *
     * @ORM\Column(type="integer", name="licence_id")
     */
    protected $licenceId;

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
     * Get the identifier
     *
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
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
     * Get the category ID
     *
     * @return int
     */
    public function getCategory()
    {
        return $this->category;
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
     * Get the sub category ID
     *
     * @return int
     */
    public function getDocumentSubCategory()
    {
        return $this->documentSubCategory;
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
     * Get the document file type
     *
     * @return string
     */
    public function getDocumentType()
    {
        return $this->documentType;
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
     * Get the licence ID (if applicable)
     *
     * @return int
     */
    public function getLicenceId()
    {
        return $this->licenceId;
    }
}

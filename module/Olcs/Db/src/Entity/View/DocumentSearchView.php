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
     * Get the is closed
     *
     * @return unknown
     */
    public function getIsClosed()
    {
        return $this->isClosed;
    }

    /**
     * Get the urgent flag
     *
     * @return unknown
     */
    public function getUrgent()
    {
        return $this->urgent;
    }

    /**
     * Get the action date
     *
     * @return \DateTime
     */
    public function getActionDate()
    {
        return $this->actionDate;
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
     * Get the name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
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
    public function getSubCategory()
    {
        return $this->subCategory;
    }

    /**
     * Get the sub category name
     *
     * @return string
     */
    public function getSubCategoryName()
    {
        return $this->subCategoryName;
    }

    /**
     * Get the task's user ID
     *
     * @return int
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * Get the task's user name
     *
     * @return string
     */
    public function getOwnerName()
    {
        return $this->ownerName;
    }

    /**
     * Get the task's team ID
     *
     * @return int
     */
    public function getTeam()
    {
        return $this->team;
    }

    /**
     * Get the number of licences relating to a task
     *
     * @return int
     */
    public function getLicenceCount()
    {
        return $this->licenceCount;
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

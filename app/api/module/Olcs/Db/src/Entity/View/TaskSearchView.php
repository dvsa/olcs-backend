<?php

/**
 * Task Search View
 *
 * @NOTE: This walks and talks like an entity but be warned, it is backed
 * by a view. As such it is is nicely readable and searchable, but writes
 * are a no go.
 *
 * You'll notice that the entity has no setters; this is intentional to
 * try and prevent accidental writes. It's marked as readOnly too to
 * prevent doctrine including it in any flushes
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */

namespace Olcs\Db\Entity\View;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Interfaces;

/**
 * Task Search View
 *
 * @ORM\Entity(readOnly=true)
 * @ORM\Table(name="task_search_view")
 */
class TaskSearchView implements Interfaces\EntityInterface
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
     * Is closed
     *
     * @var unknown
     *
     * @ORM\Column(type="yesno", name="is_closed")
     */
    protected $isClosed = 0;

    /**
     * Is urgent
     *
     * @var unknown
     *
     * @ORM\Column(type="yesno", name="urgent")
     */
    protected $urgent = 0;

    /**
     * Action date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="action_date")
     */
    protected $actionDate;

    /**
     * Identifier
     *
     * @var string
     *
     * @ORM\Column(type="string", name="link_display")
     */
    protected $identifier;

    /**
     * Name
     *
     * @var string
     *
     * @ORM\Column(type="string", name="name_display")
     */
    protected $name;

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
     * @var int
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
     * @ORM\Column(type="string", name="task_sub_category_id")
     */
    protected $subCategory;

    /**
     * Sub Category Name
     *
     * @var string
     *
     * @ORM\Column(type="string", name="task_sub_category_name")
     */
    protected $subCategoryName;

    /**
     * Owner ID
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="assigned_to_user_id")
     */
    protected $owner;

    /**
     * Owner
     *
     * @var string
     *
     * @ORM\Column(type="string", name="user_name")
     */
    protected $ownerName;

    /**
     * Team ID
     *
     * @var string
     *
     * @ORM\Column(type="integer", name="assigned_to_team_id")
     */
    protected $team;

    /**
     * Licence Count
     *
     * @var string
     *
     * @ORM\Column(type="integer", name="licence_count")
     */
    protected $licenceCount;

    /**
     * Name
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="link_id")
     */
    protected $linkId;

    /**
     * Name
     *
     * @var string
     *
     * @ORM\Column(type="string", name="link_type")
     */
    protected $linkType;

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
     * Get link id
     *
     * @return int
     */
    public function getLinkId()
    {
        return $this->linkId;
    }

    /**
     * Get link type
     *
     * @return string
     */
    public function getLinkType()
    {
        return $this->linkType;
    }
}

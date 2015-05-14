<?php

/**
 * Create Task
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\Command\Task;

use Dvsa\Olcs\Transfer\Command\AbstractCommand;

/**
 * Create Task
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class CreateTask extends AbstractCommand
{
    protected $category;

    protected $subCategory;

    protected $description;

    protected $actionDate;

    protected $assignedToUser;

    protected $assignedToTeam;

    protected $isClosed = false;

    protected $urgent = false;

    protected $application;

    protected $licence;

    /**
     * @return mixed
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param mixed $category
     */
    public function setCategory($category)
    {
        $this->category = $category;
    }

    /**
     * @return mixed
     */
    public function getSubCategory()
    {
        return $this->subCategory;
    }

    /**
     * @param mixed $subCategory
     */
    public function setSubCategory($subCategory)
    {
        $this->subCategory = $subCategory;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getActionDate()
    {
        return $this->actionDate;
    }

    /**
     * @param mixed $actionDate
     */
    public function setActionDate($actionDate)
    {
        $this->actionDate = $actionDate;
    }

    /**
     * @return mixed
     */
    public function getAssignedToUser()
    {
        return $this->assignedToUser;
    }

    /**
     * @param mixed $assignedToUser
     */
    public function setAssignedToUser($assignedToUser)
    {
        $this->assignedToUser = $assignedToUser;
    }

    /**
     * @return mixed
     */
    public function getAssignedToTeam()
    {
        return $this->assignedToTeam;
    }

    /**
     * @param mixed $assignedToTeam
     */
    public function setAssignedToTeam($assignedToTeam)
    {
        $this->assignedToTeam = $assignedToTeam;
    }

    /**
     * @return boolean
     */
    public function getIsClosed()
    {
        return $this->isClosed;
    }

    /**
     * @param boolean $isClosed
     */
    public function setIsClosed($isClosed)
    {
        $this->isClosed = $isClosed;
    }

    /**
     * @return boolean
     */
    public function getUrgent()
    {
        return $this->urgent;
    }

    /**
     * @param boolean $urgent
     */
    public function setUrgent($urgent)
    {
        $this->urgent = $urgent;
    }

    /**
     * @return mixed
     */
    public function getApplication()
    {
        return $this->application;
    }

    /**
     * @param mixed $application
     */
    public function setApplication($application)
    {
        $this->application = $application;
    }

    /**
     * @return mixed
     */
    public function getLicence()
    {
        return $this->licence;
    }

    /**
     * @param mixed $licence
     */
    public function setLicence($licence)
    {
        $this->licence = $licence;
    }
}

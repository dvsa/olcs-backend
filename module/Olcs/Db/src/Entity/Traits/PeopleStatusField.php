<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * People status field trait
 *
 * Auto-Generated (Shared between 2 entities)
 */
trait PeopleStatusField
{
    /**
     * People status
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="people_status", nullable=true)
     */
    protected $peopleStatus;

    /**
     * Set the people status
     *
     * @param int $peopleStatus
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setPeopleStatus($peopleStatus)
    {
        $this->peopleStatus = $peopleStatus;

        return $this;
    }

    /**
     * Get the people status
     *
     * @return int
     */
    public function getPeopleStatus()
    {
        return $this->peopleStatus;
    }
}

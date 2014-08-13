<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Team many to one trait
 *
 * Auto-Generated (Shared between 3 entities)
 */
trait TeamManyToOne
{
    /**
     * Team
     *
     * @var \Olcs\Db\Entity\Team
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\Team", fetch="LAZY")
     * @ORM\JoinColumn(name="team_id", referencedColumnName="id")
     */
    protected $team;

    /**
     * Set the team
     *
     * @param \Olcs\Db\Entity\Team $team
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setTeam($team)
    {
        $this->team = $team;

        return $this;
    }

    /**
     * Get the team
     *
     * @return \Olcs\Db\Entity\Team
     */
    public function getTeam()
    {
        return $this->team;
    }

}

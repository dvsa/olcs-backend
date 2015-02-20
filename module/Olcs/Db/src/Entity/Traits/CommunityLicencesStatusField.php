<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Community licences status field trait
 *
 * Auto-Generated (Shared between 2 entities)
 */
trait CommunityLicencesStatusField
{
    /**
     * Community licences status
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="community_licences_status", nullable=true)
     */
    protected $communityLicencesStatus;

    /**
     * Set the community licences status
     *
     * @param int $communityLicencesStatus
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setCommunityLicencesStatus($communityLicencesStatus)
    {
        $this->communityLicencesStatus = $communityLicencesStatus;

        return $this;
    }

    /**
     * Get the community licences status
     *
     * @return int
     */
    public function getCommunityLicencesStatus()
    {
        return $this->communityLicencesStatus;
    }
}

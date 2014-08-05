<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Community lic many to one trait
 *
 * Auto-Generated (Shared between 2 entities)
 */
trait CommunityLicManyToOne
{
    /**
     * Community lic
     *
     * @var \Olcs\Db\Entity\CommunityLic
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\CommunityLic")
     * @ORM\JoinColumn(name="community_lic_id", referencedColumnName="id")
     */
    protected $communityLic;

    /**
     * Set the community lic
     *
     * @param \Olcs\Db\Entity\CommunityLic $communityLic
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setCommunityLic($communityLic)
    {
        $this->communityLic = $communityLic;

        return $this;
    }

    /**
     * Get the community lic
     *
     * @return \Olcs\Db\Entity\CommunityLic
     */
    public function getCommunityLic()
    {
        return $this->communityLic;
    }
}

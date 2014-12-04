<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Local authority many to one trait
 *
 * Auto-Generated (Shared between 2 entities)
 */
trait LocalAuthorityManyToOne
{
    /**
     * Local authority
     *
     * @var \Olcs\Db\Entity\LocalAuthority
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\LocalAuthority", fetch="LAZY")
     * @ORM\JoinColumn(name="local_authority_id", referencedColumnName="id", nullable=true)
     */
    protected $localAuthority;

    /**
     * Set the local authority
     *
     * @param \Olcs\Db\Entity\LocalAuthority $localAuthority
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setLocalAuthority($localAuthority)
    {
        $this->localAuthority = $localAuthority;

        return $this;
    }

    /**
     * Get the local authority
     *
     * @return \Olcs\Db\Entity\LocalAuthority
     */
    public function getLocalAuthority()
    {
        return $this->localAuthority;
    }
}

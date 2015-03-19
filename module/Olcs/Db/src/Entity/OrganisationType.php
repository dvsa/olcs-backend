<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * OrganisationType Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="organisation_type",
 *    indexes={
 *        @ORM\Index(name="ix_organisation_type_org_type_id", columns={"org_type_id"}),
 *        @ORM\Index(name="ix_organisation_type_org_person_type_id", columns={"org_person_type_id"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="uk_org_person", columns={"org_type_id","org_person_type_id"})
 *    }
 * )
 */
class OrganisationType implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity;

    /**
     * Org person type
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData")
     * @ORM\JoinColumn(name="org_person_type_id", referencedColumnName="id", nullable=false)
     */
    protected $orgPersonType;

    /**
     * Org type
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData")
     * @ORM\JoinColumn(name="org_type_id", referencedColumnName="id", nullable=false)
     */
    protected $orgType;

    /**
     * Set the org person type
     *
     * @param \Olcs\Db\Entity\RefData $orgPersonType
     * @return OrganisationType
     */
    public function setOrgPersonType($orgPersonType)
    {
        $this->orgPersonType = $orgPersonType;

        return $this;
    }

    /**
     * Get the org person type
     *
     * @return \Olcs\Db\Entity\RefData
     */
    public function getOrgPersonType()
    {
        return $this->orgPersonType;
    }

    /**
     * Set the org type
     *
     * @param \Olcs\Db\Entity\RefData $orgType
     * @return OrganisationType
     */
    public function setOrgType($orgType)
    {
        $this->orgType = $orgType;

        return $this;
    }

    /**
     * Get the org type
     *
     * @return \Olcs\Db\Entity\RefData
     */
    public function getOrgType()
    {
        return $this->orgType;
    }
}

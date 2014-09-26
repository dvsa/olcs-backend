<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * OppositionGrounds Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="opposition_grounds",
 *    indexes={
 *        @ORM\Index(name="IDX_3591EE4258C0D9E2", columns={"grounds"}),
 *        @ORM\Index(name="IDX_3591EE42DE12AB56", columns={"created_by"}),
 *        @ORM\Index(name="IDX_3591EE42B4BE57B7", columns={"opposition_id"}),
 *        @ORM\Index(name="IDX_3591EE4265CF370E", columns={"last_modified_by"})
 *    }
 * )
 */
class OppositionGrounds implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\CreatedByManyToOne,
        Traits\OppositionManyToOne,
        Traits\LastModifiedByManyToOne,
        Traits\IsRepresentationField,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Grounds
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData", fetch="LAZY")
     * @ORM\JoinColumn(name="grounds", referencedColumnName="id", nullable=false)
     */
    protected $grounds;

    /**
     * Set the grounds
     *
     * @param \Olcs\Db\Entity\RefData $grounds
     * @return OppositionGrounds
     */
    public function setGrounds($grounds)
    {
        $this->grounds = $grounds;

        return $this;
    }

    /**
     * Get the grounds
     *
     * @return \Olcs\Db\Entity\RefData
     */
    public function getGrounds()
    {
        return $this->grounds;
    }
}

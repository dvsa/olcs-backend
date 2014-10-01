<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * Opposer Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="opposer",
 *    indexes={
 *        @ORM\Index(name="IDX_8111B0D2C025DC", columns={"opposer_type"}),
 *        @ORM\Index(name="IDX_8111B0D265CF370E", columns={"last_modified_by"}),
 *        @ORM\Index(name="IDX_8111B0D2DE12AB56", columns={"created_by"}),
 *        @ORM\Index(name="IDX_8111B0D27CA35EB5", columns={"contact_details_id"})
 *    }
 * )
 */
class Opposer implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\CreatedByManyToOne,
        Traits\LastModifiedByManyToOne,
        Traits\ContactDetailsManyToOne,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Opposer type
     *
     * @var \Olcs\Db\Entity\RefData
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\RefData", fetch="LAZY")
     * @ORM\JoinColumn(name="opposer_type", referencedColumnName="id", nullable=true)
     */
    protected $opposerType;

    /**
     * Set the opposer type
     *
     * @param \Olcs\Db\Entity\RefData $opposerType
     * @return Opposer
     */
    public function setOpposerType($opposerType)
    {
        $this->opposerType = $opposerType;

        return $this;
    }

    /**
     * Get the opposer type
     *
     * @return \Olcs\Db\Entity\RefData
     */
    public function getOpposerType()
    {
        return $this->opposerType;
    }
}

<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * PostcodeEnforcementArea Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="postcode_enforcement_area",
 *    indexes={
 *        @ORM\Index(name="ix_postcode_enforcement_area_enforcement_area_id", columns={"enforcement_area_id"}),
 *        @ORM\Index(name="ix_postcode_enforcement_area_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_postcode_enforcement_area_last_modified_by", columns={"last_modified_by"})
 *    },
 *    uniqueConstraints={
 *        @ORM\UniqueConstraint(name="uk_postcode_enforcement_area_enforcement_area_id_postcode_id", columns={"enforcement_area_id","postcode_id"})
 *    }
 * )
 */
class PostcodeEnforcementArea implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\CreatedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\EnforcementAreaManyToOne,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Postcode id
     *
     * @var string
     *
     * @ORM\Column(type="string", name="postcode_id", length=8, nullable=false)
     */
    protected $postcodeId;

    /**
     * Set the postcode id
     *
     * @param string $postcodeId
     * @return PostcodeEnforcementArea
     */
    public function setPostcodeId($postcodeId)
    {
        $this->postcodeId = $postcodeId;

        return $this;
    }

    /**
     * Get the postcode id
     *
     * @return string
     */
    public function getPostcodeId()
    {
        return $this->postcodeId;
    }
}

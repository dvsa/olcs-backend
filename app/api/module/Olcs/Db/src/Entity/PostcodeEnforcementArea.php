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
 *        @ORM\Index(name="fk_PostcodeVehicleInspectorate_VehicleInspectorate1_idx", columns={"enforcement_area_id"}),
 *        @ORM\Index(name="fk_postcode_enforcement_area_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_postcode_enforcement_area_user2_idx", columns={"last_modified_by"})
 *    }
 * )
 */
class PostcodeEnforcementArea implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\LastModifiedByManyToOne,
        Traits\CreatedByManyToOne,
        Traits\EnforcementAreaOneToOne,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Identifier - Postcode id
     *
     * @var string
     *
     * @ORM\Id
     * @ORM\Column(type="string", name="postcode_id", length=8)
     */
    protected $postcodeId;

    /**
     * Set the postcode id
     *
     * @param string $postcodeId
     * @return \Olcs\Db\Entity\PostcodeEnforcementArea
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

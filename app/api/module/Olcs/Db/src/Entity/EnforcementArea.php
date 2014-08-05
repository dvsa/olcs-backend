<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * EnforcementArea Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="enforcement_area",
 *    indexes={
 *        @ORM\Index(name="fk_enforcement_area_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_enforcement_area_user2_idx", columns={"last_modified_by"})
 *    }
 * )
 */
class EnforcementArea implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\LastModifiedByManyToOne,
        Traits\CreatedByManyToOne,
        Traits\Name70Field,
        Traits\EmailAddress60Field,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Identifier - Id
     *
     * @var string
     *
     * @ORM\Id
     * @ORM\Column(type="string", name="id", length=4)
     */
    protected $id;

    /**
     * Set the id
     *
     * @param string $id
     * @return \Olcs\Db\Entity\EnforcementArea
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the id
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }
}

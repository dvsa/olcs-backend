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
 *        @ORM\Index(name="IDX_5975A7EDDE12AB56", columns={"created_by"}),
 *        @ORM\Index(name="IDX_5975A7ED65CF370E", columns={"last_modified_by"})
 *    }
 * )
 */
class EnforcementArea implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\CreatedByManyToOne,
        Traits\LastModifiedByManyToOne,
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
     * @return EnforcementArea
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

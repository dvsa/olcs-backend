<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * Permission Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="permission",
 *    indexes={
 *        @ORM\Index(name="ix_permission_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_permission_last_modified_by", columns={"last_modified_by"})
 *    }
 * )
 */
class Permission implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\Code5Field,
        Traits\CreatedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Name
     *
     * @var string
     *
     * @ORM\Column(type="string", name="name", length=45, nullable=false)
     */
    protected $name;

    /**
     * Set the name
     *
     * @param string $name
     * @return Permission
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get the name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}

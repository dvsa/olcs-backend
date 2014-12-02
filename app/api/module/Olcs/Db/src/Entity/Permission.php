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
 *        @ORM\Index(name="fk_permission_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_permission_user2_idx", columns={"last_modified_by"})
 *    }
 * )
 */
class Permission implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CreatedByManyToOne,
        Traits\Name45Field,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Code
     *
     * @var string
     *
     * @ORM\Column(type="string", name="code", length=5, nullable=false)
     */
    protected $code;

    /**
     * Set the code
     *
     * @param string $code
     * @return Permission
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get the code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }
}

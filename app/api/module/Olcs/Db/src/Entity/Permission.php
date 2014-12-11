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
        Traits\CreatedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
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
     * Name
     *
     * @var string
     *
     * @ORM\Column(type="string", name="name", length=45, nullable=false)
     */
    protected $name;

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

<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Olcs\Db\Entity\Traits;

/**
 * Role Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="role",
 *    indexes={
 *        @ORM\Index(name="fk_role_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_role_user2_idx", columns={"last_modified_by"})
 *    }
 * )
 */
class Role implements Interfaces\EntityInterface
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
     * @ORM\Column(type="string", name="code", length=5, nullable=true)
     */
    protected $code;

    /**
     * Role
     *
     * @var string
     *
     * @ORM\Column(type="string", name="role", length=100, nullable=true)
     */
    protected $role;

    /**
     * Role permission
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Olcs\Db\Entity\RolePermission", mappedBy="role")
     */
    protected $rolePermissions;

    /**
     * Initialise the collections
     */
    public function __construct()
    {
        $this->rolePermissions = new ArrayCollection();
    }

    /**
     * Set the code
     *
     * @param string $code
     * @return Role
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
     * Set the role
     *
     * @param string $role
     * @return Role
     */
    public function setRole($role)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Get the role
     *
     * @return string
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Set the role permission
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $rolePermissions
     * @return Role
     */
    public function setRolePermissions($rolePermissions)
    {
        $this->rolePermissions = $rolePermissions;

        return $this;
    }

    /**
     * Get the role permissions
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getRolePermissions()
    {
        return $this->rolePermissions;
    }

    /**
     * Add a role permissions
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $rolePermissions
     * @return Role
     */
    public function addRolePermissions($rolePermissions)
    {
        if ($rolePermissions instanceof ArrayCollection) {
            $this->rolePermissions = new ArrayCollection(
                array_merge(
                    $this->rolePermissions->toArray(),
                    $rolePermissions->toArray()
                )
            );
        } elseif (!$this->rolePermissions->contains($rolePermissions)) {
            $this->rolePermissions->add($rolePermissions);
        }

        return $this;
    }

    /**
     * Remove a role permissions
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $rolePermissions
     * @return Role
     */
    public function removeRolePermissions($rolePermissions)
    {
        if ($this->rolePermissions->contains($rolePermissions)) {
            $this->rolePermissions->removeElement($rolePermissions);
        }

        return $this;
    }
}

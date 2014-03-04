<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Olcs\Db\EntityTraits;

/**
 * @ORM\Table(name="role")
 * @ORM\Entity
 */
class Role extends AbstractEntity
{
    use EntityTraits\Handle;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=100, nullable=true)
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\ManyToMany(targetEntity="Olcs\Db\Entity\User", mappedBy="roles")
     */
    protected $users;

    /**
     * @var string
     *
     * @ORM\ManyToMany(targetEntity="Olcs\Db\Entity\Permission", inversedBy="roles", fetch="LAZY")
     * @ORM\JoinTable(name="role_permission")
     */
    protected $permissions;

    /**
     * Constructor
     *
     * @return void
     */
    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->permissions = new ArrayCollection();
    }

    /**
     * Non-owning side.
     *
     * @param \Olcs\Db\Entity\User $user
     *
     * @return \Olcs\Db\Entity\Role
     */
    public function addUser(User $user)
    {
        $ths->users[] = $user;
        return $this;
    }

    /**
     * Adds a permission to the role.
     *
     * @param Permission $permission
     *
     * @return \Olcs\Db\Entity\Role
     */
    public function addPermission(Permission $permission)
    {
        $permission->addRole($this);

        $ths->permissions[] = $permission;

        return $this;
    }

    /**
     * Gets the related permnissions.
     *
     * @return string
     */
    public function getPermissions()
    {
        return $this->permissions;
    }
}

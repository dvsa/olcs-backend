<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Olcs\Db\EntityTraits;

/**
 * @ORM\Table(name="permission")
 * @ORM\Entity
 */
class Permission extends AbstractEntity
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
     * @ORM\ManyToMany(targetEntity="Permission", mappedBy="permissions")
     */
    protected $roles;

    /**
     * Constructor
     *
     * @return void
     */
    public function __construct()
    {
        $this->roles = new ArrayCollection();
    }

    /**
     * Sets the name property.
     *
     * @param unknown_type $name
     *
     * @return \Olcs\Db\Entity\Permission
     */
    public function setName($name)
    {
       $this->name = $name;
       return $this;
    }

    /**
     * Gets the value of the name property.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Non-owning side.
     *
     * @param \Olcs\Db\Entity\Role $role
     *
     * @return \Olcs\Db\Entity\Permission
     */
    public function addRole(Role $role)
    {
        $ths->roles[] = $role;

        return $this;
    }

    /**
     * Gets a list of roles associated with this permission.
     *
     * @return string
     */
    public function getRoles()
    {
        return $this->roles;
    }
}

<?php

namespace Dvsa\Olcs\Api\Entity\User;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Role Abstract Entity
 *
 * Auto-Generated
 *
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="role",
 *    indexes={
 *        @ORM\Index(name="ix_role_created_by", columns={"created_by"}),
 *        @ORM\Index(name="ix_role_last_modified_by", columns={"last_modified_by"})
 *    }
 * )
 */
abstract class AbstractRole
{

    /**
     * Code
     *
     * @var string
     *
     * @ORM\Column(type="string", name="code", length=5, nullable=false)
     */
    protected $code;

    /**
     * Created by
     *
     * @var \Dvsa\Olcs\Api\Entity\User\User
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\User\User", fetch="LAZY")
     * @ORM\JoinColumn(name="created_by", referencedColumnName="id", nullable=true)
     */
    protected $createdBy;

    /**
     * Created on
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="created_on", nullable=true)
     */
    protected $createdOn;

    /**
     * Description
     *
     * @var string
     *
     * @ORM\Column(type="string", name="description", length=255, nullable=true)
     */
    protected $description;

    /**
     * Identifier - Id
     *
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer", name="id")
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;

    /**
     * Last modified by
     *
     * @var \Dvsa\Olcs\Api\Entity\User\User
     *
     * @ORM\ManyToOne(targetEntity="Dvsa\Olcs\Api\Entity\User\User", fetch="LAZY")
     * @ORM\JoinColumn(name="last_modified_by", referencedColumnName="id", nullable=true)
     */
    protected $lastModifiedBy;

    /**
     * Last modified on
     *
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", name="last_modified_on", nullable=true)
     */
    protected $lastModifiedOn;

    /**
     * Role
     *
     * @var string
     *
     * @ORM\Column(type="string", name="role", length=100, nullable=false)
     */
    protected $role;

    /**
     * Version
     *
     * @var int
     *
     * @ORM\Column(type="smallint", name="version", nullable=false, options={"default": 1})
     * @ORM\Version
     */
    protected $version = 1;

    /**
     * Role permission
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Dvsa\Olcs\Api\Entity\User\RolePermission", mappedBy="role")
     */
    protected $rolePermissions;

    /**
     * Role
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Dvsa\Olcs\Api\Entity\User\UserRole", mappedBy="role")
     */
    protected $roles;

    /**
     * Initialise the collections
     */
    public function __construct()
    {
        $this->initCollections();
    }

    public function initCollections()
    {
        $this->rolePermissions = new ArrayCollection();
        $this->roles = new ArrayCollection();
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
     * Set the created by
     *
     * @param \Dvsa\Olcs\Api\Entity\User\User $createdBy
     * @return Role
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get the created by
     *
     * @return \Dvsa\Olcs\Api\Entity\User\User
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Set the created on
     *
     * @param \DateTime $createdOn
     * @return Role
     */
    public function setCreatedOn($createdOn)
    {
        $this->createdOn = $createdOn;

        return $this;
    }

    /**
     * Get the created on
     *
     * @return \DateTime
     */
    public function getCreatedOn()
    {
        return $this->createdOn;
    }

    /**
     * Set the description
     *
     * @param string $description
     * @return Role
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get the description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set the id
     *
     * @param int $id
     * @return Role
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set the last modified by
     *
     * @param \Dvsa\Olcs\Api\Entity\User\User $lastModifiedBy
     * @return Role
     */
    public function setLastModifiedBy($lastModifiedBy)
    {
        $this->lastModifiedBy = $lastModifiedBy;

        return $this;
    }

    /**
     * Get the last modified by
     *
     * @return \Dvsa\Olcs\Api\Entity\User\User
     */
    public function getLastModifiedBy()
    {
        return $this->lastModifiedBy;
    }

    /**
     * Set the last modified on
     *
     * @param \DateTime $lastModifiedOn
     * @return Role
     */
    public function setLastModifiedOn($lastModifiedOn)
    {
        $this->lastModifiedOn = $lastModifiedOn;

        return $this;
    }

    /**
     * Get the last modified on
     *
     * @return \DateTime
     */
    public function getLastModifiedOn()
    {
        return $this->lastModifiedOn;
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
     * Set the version
     *
     * @param int $version
     * @return Role
     */
    public function setVersion($version)
    {
        $this->version = $version;

        return $this;
    }

    /**
     * Get the version
     *
     * @return int
     */
    public function getVersion()
    {
        return $this->version;
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

    /**
     * Set the role
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $roles
     * @return Role
     */
    public function setRoles($roles)
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * Get the roles
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * Add a roles
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $roles
     * @return Role
     */
    public function addRoles($roles)
    {
        if ($roles instanceof ArrayCollection) {
            $this->roles = new ArrayCollection(
                array_merge(
                    $this->roles->toArray(),
                    $roles->toArray()
                )
            );
        } elseif (!$this->roles->contains($roles)) {
            $this->roles->add($roles);
        }

        return $this;
    }

    /**
     * Remove a roles
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $roles
     * @return Role
     */
    public function removeRoles($roles)
    {
        if ($this->roles->contains($roles)) {
            $this->roles->removeElement($roles);
        }

        return $this;
    }

    /**
     * Set the createdOn field on persist
     *
     * @ORM\PrePersist
     */
    public function setCreatedOnBeforePersist()
    {
        $this->createdOn = new \DateTime();
    }

    /**
     * Set the lastModifiedOn field on persist
     *
     * @ORM\PreUpdate
     */
    public function setLastModifiedOnBeforeUpdate()
    {
        $this->lastModifiedOn = new \DateTime();
    }

    /**
     * Clear properties
     *
     * @param type $properties
     */
    public function clearProperties($properties = array())
    {
        foreach ($properties as $property) {

            if (property_exists($this, $property)) {
                if ($this->$property instanceof Collection) {

                    $this->$property = new ArrayCollection(array());

                } else {

                    $this->$property = null;
                }
            }
        }
    }
}

<?php
namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\MappedSuperclass
 */
class AbstractEntity
{
    /**
     * @var \Olcs\Db\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\User", fetch="LAZY")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="created_by", referencedColumnName="id", nullable=true)
     * })
     */
    private $createdBy;

    /**
     * @var datetime
     *
     * @ORM\Column(name="created_on", type="datetime", nullable=true)
     */
    private $createdOn;

    /**
     * @var \Olcs\Db\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="Olcs\Db\Entity\User", fetch="LAZY")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="last_updated_by", referencedColumnName="id", nullable=true)
     * })
     */
    private $lastUpdatedBy;

    /**
     * @var datetime
     *
     * @ORM\Column(name="last_updated_on", type="datetime", nullable=true)
     */
    private $lastUpdatedOn;

    /**
     * @ORM\Version @ORM\Column(type="integer")
     */
    private $version;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_deleted", type="boolean", nullable=true)
     */
    private $isDeleted;

    /**
     * Run this method on prePersist.
     *
     * @ORM\PrePersist
     *
     * @return \Olcs\Db\Entity\AbstractEntity
     */
    public function setDefaultsOnPrePersist()
    {
        $date = new \DateTime('NOW');

        $this->setCreatedOn($date);
        $this->setLastUpdatedOn($date);
        $this->setVersion(1);

        return $this;
    }

    /**
     * Set createdBy
     *
     * @param \Olcs\Db\Entity\User $createdBy
     *
     * @return \Olcs\Db\Entity\AbstractEntity
     */
    public function setCreatedBy(User $createdBy)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get createdBy
     *
     * @return \Olcs\Db\Entity\User
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return \Olcs\Db\Entity\AbstractEntity
     */
    public function setCreatedOn($createdOn)
    {
        $this->createdOn = $createdOn;

        return $this;
    }

    /**
     * Get createdBy
     *
     * @return \DateTime
     */
    public function getCreatedOn()
    {
        return $this->createdOn;
    }

    /**
     * Set lastUpdatedBy
     *
     * @param \Olcs\Db\Entity\User $lastUpdatedBy
     *
     * @return \Olcs\Db\Entity\AbstractEntity
     */
    public function setLastUpdatedBy(User $lastUpdatedBy)
    {
        $this->lastUpdatedBy = $lastUpdatedBy;

        return $this;
    }

    /**
     * Get lastUpdatedBy
     *
     * @return \Olcs\Db\Entity\User
     */
    public function getLastUpdatedBy()
    {
        return $this->lastUpdatedBy;
    }

    /**
     * Set lastUpdatedOn
     *
     * @param string $lastUpdatedOn
     *
     * @return \Olcs\Db\Entity\AbstractEntity
     */
    public function setLastUpdatedOn($lastUpdatedOn)
    {
        $this->lastUpdatedOn = $lastUpdatedOn;

        return $this;
    }

    /**
     * Get lastUpdatedOn
     *
     * @return \DateTime
     */
    public function getLastUpdatedOn()
    {
        return $this->lastUpdatedOn;
    }

    /**
     * Set version
     *
     * @param integer version
     *
     * @return \Olcs\Db\Entity\AbstractEntity
     */
    public function setVersion($version)
    {
        $this->version = $version;

        return $this;
    }

    /**
     * Get version
     *
     * @return integer
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * Set isDeleted
     *
     * @param integer isDeleted
     *
     * @return \Olcs\Db\Entity\AbstractEntity
     */
    public function setIsDeleted($isDeleted)
    {
        $this->isDeleted = $isDeleted;

        return $this;
    }

    /**
     * Get isDeleted
     *
     * @return integer
     */
    public function getIsDeleted()
    {
        return $this->isDeleted;
    }
}

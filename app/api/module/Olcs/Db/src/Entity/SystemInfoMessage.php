<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * SystemInfoMessage Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="system_info_message",
 *    indexes={
 *        @ORM\Index(name="fk_system_info_message_user1_idx", columns={"created_by"}),
 *        @ORM\Index(name="fk_system_info_message_user2_idx", columns={"last_modified_by"})
 *    }
 * )
 */
class SystemInfoMessage implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\LastModifiedByManyToOne,
        Traits\CreatedByManyToOne,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Is internal
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="is_internal", nullable=false)
     */
    protected $isInternal;

    /**
     * Activate date
     *
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="activate_date", nullable=false)
     */
    protected $activateDate;

    /**
     * Is deleted
     *
     * @var string
     *
     * @ORM\Column(type="yesno", name="is_deleted", nullable=false)
     */
    protected $isDeleted = 0;

    /**
     * Description
     *
     * @var string
     *
     * @ORM\Column(type="string", name="description", length=1024, nullable=false)
     */
    protected $description;

    /**
     * Importance
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="importance", nullable=true)
     */
    protected $importance;

    /**
     * Set the is internal
     *
     * @param string $isInternal
     * @return SystemInfoMessage
     */
    public function setIsInternal($isInternal)
    {
        $this->isInternal = $isInternal;

        return $this;
    }

    /**
     * Get the is internal
     *
     * @return string
     */
    public function getIsInternal()
    {
        return $this->isInternal;
    }

    /**
     * Set the activate date
     *
     * @param \DateTime $activateDate
     * @return SystemInfoMessage
     */
    public function setActivateDate($activateDate)
    {
        $this->activateDate = $activateDate;

        return $this;
    }

    /**
     * Get the activate date
     *
     * @return \DateTime
     */
    public function getActivateDate()
    {
        return $this->activateDate;
    }

    /**
     * Set the is deleted
     *
     * @param string $isDeleted
     * @return SystemInfoMessage
     */
    public function setIsDeleted($isDeleted)
    {
        $this->isDeleted = $isDeleted;

        return $this;
    }

    /**
     * Get the is deleted
     *
     * @return string
     */
    public function getIsDeleted()
    {
        return $this->isDeleted;
    }

    /**
     * Set the description
     *
     * @param string $description
     * @return SystemInfoMessage
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
     * Set the importance
     *
     * @param int $importance
     * @return SystemInfoMessage
     */
    public function setImportance($importance)
    {
        $this->importance = $importance;

        return $this;
    }

    /**
     * Get the importance
     *
     * @return int
     */
    public function getImportance()
    {
        return $this->importance;
    }
}

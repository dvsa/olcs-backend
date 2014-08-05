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
        Traits\IsDeletedField,
        Traits\Description1024Field,
        Traits\CustomCreatedOnField,
        Traits\CustomLastModifiedOnField,
        Traits\CustomVersionField;

    /**
     * Is internal
     *
     * @var boolean
     *
     * @ORM\Column(type="yesnonull", name="is_internal", nullable=false)
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
     * @param boolean $isInternal
     * @return \Olcs\Db\Entity\SystemInfoMessage
     */
    public function setIsInternal($isInternal)
    {
        $this->isInternal = $isInternal;

        return $this;
    }

    /**
     * Get the is internal
     *
     * @return boolean
     */
    public function getIsInternal()
    {
        return $this->isInternal;
    }

    /**
     * Set the activate date
     *
     * @param \DateTime $activateDate
     * @return \Olcs\Db\Entity\SystemInfoMessage
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
     * Set the importance
     *
     * @param int $importance
     * @return \Olcs\Db\Entity\SystemInfoMessage
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

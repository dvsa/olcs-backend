<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * ApplicationActionRef Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="application_action_ref")
 */
class ApplicationActionRef implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\IdIdentity,
        Traits\Description45Field;

    /**
     * Display order
     *
     * @var int
     *
     * @ORM\Column(type="integer", name="display_order", nullable=false)
     */
    protected $displayOrder;

    /**
     * Default received
     *
     * @var string
     *
     * @ORM\Column(type="yesnonull", name="default_received", nullable=true)
     */
    protected $defaultReceived;

    /**
     * Default approved
     *
     * @var string
     *
     * @ORM\Column(type="yesnonull", name="default_approved", nullable=true)
     */
    protected $defaultApproved;

    /**
     * Default applicable
     *
     * @var string
     *
     * @ORM\Column(type="string", name="default_applicable", length=45, nullable=true)
     */
    protected $defaultApplicable;


    /**
     * Set the display order
     *
     * @param int $displayOrder
     * @return ApplicationActionRef
     */
    public function setDisplayOrder($displayOrder)
    {
        $this->displayOrder = $displayOrder;

        return $this;
    }

    /**
     * Get the display order
     *
     * @return int
     */
    public function getDisplayOrder()
    {
        return $this->displayOrder;
    }

    /**
     * Set the default received
     *
     * @param string $defaultReceived
     * @return ApplicationActionRef
     */
    public function setDefaultReceived($defaultReceived)
    {
        $this->defaultReceived = $defaultReceived;

        return $this;
    }

    /**
     * Get the default received
     *
     * @return string
     */
    public function getDefaultReceived()
    {
        return $this->defaultReceived;
    }

    /**
     * Set the default approved
     *
     * @param string $defaultApproved
     * @return ApplicationActionRef
     */
    public function setDefaultApproved($defaultApproved)
    {
        $this->defaultApproved = $defaultApproved;

        return $this;
    }

    /**
     * Get the default approved
     *
     * @return string
     */
    public function getDefaultApproved()
    {
        return $this->defaultApproved;
    }

    /**
     * Set the default applicable
     *
     * @param string $defaultApplicable
     * @return ApplicationActionRef
     */
    public function setDefaultApplicable($defaultApplicable)
    {
        $this->defaultApplicable = $defaultApplicable;

        return $this;
    }

    /**
     * Get the default applicable
     *
     * @return string
     */
    public function getDefaultApplicable()
    {
        return $this->defaultApplicable;
    }
}

<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
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
     * Application
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Olcs\Db\Entity\Application", mappedBy="applicationActionRefs")
     */
    protected $applications;

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
     * @var boolean
     *
     * @ORM\Column(type="yesnonull", name="default_received", nullable=true)
     */
    protected $defaultReceived;

    /**
     * Default approved
     *
     * @var boolean
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
     * Initialise the collections
     */
    public function __construct()
    {
        $this->applications = new ArrayCollection();
    }

    /**
     * Set the application
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $applications

     * @return \Olcs\Db\Entity\ApplicationActionRef
     */
    public function setApplications($applications)
    {
        $this->applications = $applications;

        return $this;
    }

    /**
     * Get the application
     *
     * @return \Doctrine\Common\Collections\ArrayCollection

     */
    public function getApplications()
    {
        return $this->applications;
    }

    /**
     * Set the display order
     *
     * @param int $displayOrder
     * @return \Olcs\Db\Entity\ApplicationActionRef
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
     * @param boolean $defaultReceived
     * @return \Olcs\Db\Entity\ApplicationActionRef
     */
    public function setDefaultReceived($defaultReceived)
    {
        $this->defaultReceived = $defaultReceived;

        return $this;
    }

    /**
     * Get the default received
     *
     * @return boolean
     */
    public function getDefaultReceived()
    {
        return $this->defaultReceived;
    }

    /**
     * Set the default approved
     *
     * @param boolean $defaultApproved
     * @return \Olcs\Db\Entity\ApplicationActionRef
     */
    public function setDefaultApproved($defaultApproved)
    {
        $this->defaultApproved = $defaultApproved;

        return $this;
    }

    /**
     * Get the default approved
     *
     * @return boolean
     */
    public function getDefaultApproved()
    {
        return $this->defaultApproved;
    }

    /**
     * Set the default applicable
     *
     * @param string $defaultApplicable
     * @return \Olcs\Db\Entity\ApplicationActionRef
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

<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Action1 field trait
 *
 * Auto-Generated (Shared between 3 entities)
 */
trait Action1Field
{
    /**
     * Action
     *
     * @var string
     *
     * @ORM\Column(type="string", name="action", length=1, nullable=true)
     */
    protected $action;

    /**
     * Set the action
     *
     * @param string $action
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setAction($action)
    {
        $this->action = $action;

        return $this;
    }

    /**
     * Get the action
     *
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }
}

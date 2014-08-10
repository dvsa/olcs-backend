<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Presiding tc other45 field trait
 *
 * Auto-Generated (Shared between 2 entities)
 */
trait PresidingTcOther45Field
{
    /**
     * Presiding tc other
     *
     * @var string
     *
     * @ORM\Column(type="string", name="presiding_tc_other", length=45, nullable=true)
     */
    protected $presidingTcOther;

    /**
     * Set the presiding tc other
     *
     * @param string $presidingTcOther
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
     */
    public function setPresidingTcOther($presidingTcOther)
    {
        $this->presidingTcOther = $presidingTcOther;

        return $this;
    }

    /**
     * Get the presiding tc other
     *
     * @return string
     */
    public function getPresidingTcOther()
    {
        return $this->presidingTcOther;
    }

}

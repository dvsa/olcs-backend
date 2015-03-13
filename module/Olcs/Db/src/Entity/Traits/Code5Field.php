<?php

namespace Olcs\Db\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Code5 field trait
 *
 * Auto-Generated (Shared between 2 entities)
 */
trait Code5Field
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
     * Set the code
     *
     * @param string $code
     * @return \Olcs\Db\Entity\Interfaces\EntityInterface
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
}

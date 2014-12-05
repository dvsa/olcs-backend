<?php

namespace Olcs\Db\Entity;

use Doctrine\ORM\Mapping as ORM;
use Olcs\Db\Entity\Traits;

/**
 * SystemParameter Entity
 *
 * Auto-Generated
 *
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="system_parameter")
 */
class SystemParameter implements Interfaces\EntityInterface
{
    use Traits\CustomBaseEntity,
        Traits\Description255FieldAlt1,
        Traits\Id32Identity;

    /**
     * Param value
     *
     * @var string
     *
     * @ORM\Column(type="string", name="param_value", length=32, nullable=true)
     */
    protected $paramValue;

    /**
     * Set the param value
     *
     * @param string $paramValue
     * @return SystemParameter
     */
    public function setParamValue($paramValue)
    {
        $this->paramValue = $paramValue;

        return $this;
    }

    /**
     * Get the param value
     *
     * @return string
     */
    public function getParamValue()
    {
        return $this->paramValue;
    }
}

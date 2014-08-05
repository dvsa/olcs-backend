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
        Traits\Description255FieldAlt1;

    /**
     * Identifier - Param key
     *
     * @var string
     *
     * @ORM\Id
     * @ORM\Column(type="string", name="param_key", length=32)
     */
    protected $paramKey;

    /**
     * Param value
     *
     * @var string
     *
     * @ORM\Column(type="string", name="param_value", length=32, nullable=true)
     */
    protected $paramValue;

    /**
     * Set the param key
     *
     * @param string $paramKey
     * @return \Olcs\Db\Entity\SystemParameter
     */
    public function setParamKey($paramKey)
    {
        $this->paramKey = $paramKey;

        return $this;
    }

    /**
     * Get the param key
     *
     * @return string
     */
    public function getParamKey()
    {
        return $this->paramKey;
    }

    /**
     * Set the param value
     *
     * @param string $paramValue
     * @return \Olcs\Db\Entity\SystemParameter
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

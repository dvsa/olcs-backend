<?php

namespace Dvsa\Olcs\Api\Service\Qa\Structure;

use RuntimeException;

class Validator
{
    /** @var string */
    private $rule;

    /** @var array */
    private $params;

    /**
     * Create instance
     *
     * @param string $rule
     * @param array $params
     *
     * @return Validator
     */
    public function __construct(string $rule, array $params)
    {
        $this->rule = $rule;
        $this->params = $params;
    }

    /**
     * Whether the rule associated with this validator is the same as the specified rule
     *
     * @param string $rule
     *
     * @return bool
     */
    public function hasRule($rule)
    {
        return($this->rule == $rule);
    }

    /**
     * Get the representation of this class to be returned by the API endpoint
     *
     * @return array
     */
    public function getRepresentation()
    {
        return [
            'rule' => $this->rule,
            'params' => $this->params,
        ];
    }

    public function setParameter($name, $value)
    {
        if (!isset($this->params[$name])) {
            throw new RuntimeException('Parameter ' . $name. ' not found in validator');
        }

        $this->params[$name] = $value;
    }
}

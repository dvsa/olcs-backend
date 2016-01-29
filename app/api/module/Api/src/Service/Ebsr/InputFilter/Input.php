<?php

namespace Dvsa\Olcs\Api\Service\Ebsr\InputFilter;

use Zend\InputFilter\Input as ZendInput;

/**
 * Class Input
 * @package Olcs\Ebsr\InputFilter
 */
class Input extends ZendInput
{
    /**
     * @var mixed
     */
    protected $filteredValue;
    /**
     * @var bool
     */
    protected $hasFiltered = false;

    /**
     * @return mixed
     */
    public function getValue()
    {
        if (!$this->hasFiltered) {
            $this->filteredValue = $this->getFilterChain()->filter($this->value);
            $this->hasFiltered = true;
        }
        return $this->filteredValue;
    }

    /**
     * @param  mixed $value
     * @return Input
     */
    public function setValue($value)
    {
        $this->hasFiltered = false;
        $this->value = $value;
    }
}

<?php

namespace Dvsa\Olcs\GdsVerify\Data;

/**
 * Class Attributes
 * @package Dvsa\Olcs\GdsVerify\Data
 */
class Attributes extends \ArrayObject
{
    const DATE_OF_BIRTH = 'DATE_OF_BIRTH';
    const FIRST_NAME = 'FIRST_NAME';
    const MIDDLE_NAME = 'MIDDLE_NAME';
    const SURNAME = 'SURNAME';

    /**
     * Get full name
     *
     * @return string
     */
    public function getFullName()
    {
        $names = [];
        if (!empty($this[self::FIRST_NAME])) {
            $names[] = $this[self::FIRST_NAME];
        }
        if (!empty($this[self::MIDDLE_NAME])) {
            $names[] = $this[self::MIDDLE_NAME];
        }
        if (!empty($this[self::SURNAME])) {
            $names[] = $this[self::SURNAME];
        }

        return implode(' ', $names);
    }

    /**
     * Get Date of birth
     *
     * @return bool|\DateTime
     */
    public function getDateOfBirth()
    {
        if (!empty($this[self::DATE_OF_BIRTH])) {
            return new \DateTime($this[self::DATE_OF_BIRTH]);
        }

        return false;
    }

    /**
     * Are the attributes considered valid for a signature
     *
     * @return bool
     */
    public function isValidSignature()
    {
        return !empty($this[self::FIRST_NAME]) &&
            !empty($this[self::SURNAME]) &&
            !empty($this[self::DATE_OF_BIRTH]);
    }
}

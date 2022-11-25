<?php

declare(strict_types=1);

namespace Dvsa\Olcs\Api\Service\GovUkAccount\Data;

/**
 * @note based on the original GDS Verify version, now being used with GovUkAccount
 */
class Attributes extends \ArrayObject
{
    const DATE_OF_BIRTH = 'dateofbirth';
    const FIRST_NAME = 'firstname';
    const SURNAME = 'surname';

    /**
     * Get full name
     *
     * @return string
     */
    public function getFullName(): string
    {
        $names = [];
        if (!empty($this[self::FIRST_NAME])) {
            $names[] = $this[self::FIRST_NAME];
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
     */
    public function isValidSignature(): bool
    {
        return !empty($this[self::FIRST_NAME]) &&
            !empty($this[self::SURNAME]) &&
            !empty($this[self::DATE_OF_BIRTH]);
    }
}

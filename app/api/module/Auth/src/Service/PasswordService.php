<?php
declare(strict_types=1);

namespace Dvsa\Olcs\Auth\Service;

use Faker\Generator;

class PasswordService
{
    const MINIMUM_LENGTH = 12;
    const SYMBOL_REGEX = '[-=~!@#$%^&*()_+,./?;:]{1}';

    const ERR_MESSAGE_TOO_SHORT = 'Password length cannot be less than 12';

    /**
     * @var Generator
     */
    private $generator;

    public function __construct(Generator $generator)
    {
        $this->generator = $generator;
    }

    /**
     * Generates a password
     *
     * @return string
     */
    public function generatePassword(int $length = 12)
    {
        if ($length < static::MINIMUM_LENGTH) {
            throw new \InvalidArgumentException(static::ERR_MESSAGE_TOO_SHORT);
        }

        $fillerLength = $length - 5;

        $components = [
            strtoupper($this->generator->randomLetter),
            $this->generator->regexify(static::SYMBOL_REGEX),
            strtolower($this->generator->randomLetter),
            $this->generator->randomNumber(1),
            $this->generator->regexify(static::SYMBOL_REGEX),
            $this->generator->regexify(sprintf('[A-Za-z0-9]{%d}$', $fillerLength)),
        ];

        shuffle($components);

        return implode('', $components);
    }
}

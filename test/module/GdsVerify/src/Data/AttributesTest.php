<?php

namespace Dvsa\OlcsTest\GdsVerify\Data;

use Dvsa\Olcs\GdsVerify\Data\Attributes;

/**
 * Attributes  test
 */
class AttributesTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @dataProvider dpGetFullName
     *
     * @param array  $attributes
     * @param string $expected
     */
    public function testGetFullName($attributes, $expected)
    {
        $attributes = new Attributes($attributes);
        $this->assertSame($expected, $attributes->getFullName());
    }

    public function dpGetFullName()
    {
        return [
            [
                [Attributes::FIRST_NAME => 'John', Attributes::SURNAME => 'Smith'],
                'John Smith'
            ],
            [
                [Attributes::FIRST_NAME => 'John', Attributes::SURNAME => 'Smith'],
                'John Smith'
            ],
            [
                [Attributes::FIRST_NAME => 'John'],
                'John'
            ],
            [
                [Attributes::SURNAME => 'Smith'],
                'Smith'
            ],
            [
                [],
                ''
            ],
        ];
    }

    public function testGetDateOfBirth()
    {
        $attributes = new Attributes([Attributes::DATE_OF_BIRTH => '1992-02-28']);
        $this->assertEquals(new \DateTime('1992-02-28'), $attributes->getDateOfBirth());
    }

    public function testGetDateOfBirthEmpty()
    {
        $attributes = new Attributes([]);
        $this->assertFalse($attributes->getDateOfBirth());
    }

    /**
     * @dataProvider dpIsValidSignature
     *
     * @param $attributes
     * @param $expected
     */
    public function testIsValidSignature($attributes, $expected)
    {
        $attributes = new Attributes($attributes);
        $this->assertSame($expected, $attributes->isValidSignature());
    }

    public function dpIsValidSignature()
    {
        return [
            [
                [
                    Attributes::FIRST_NAME => 'John',
                    Attributes::SURNAME => 'Smith',
                    Attributes::DATE_OF_BIRTH => '1999-10-10'],
                true
            ],
            [
                [
                    Attributes::FIRST_NAME => 'John',
                    Attributes::SURNAME => 'Smith',
                    Attributes::DATE_OF_BIRTH => '1999-10-10'
                ],
                true
            ],
            [
                [Attributes::SURNAME => 'Smith', Attributes::DATE_OF_BIRTH => '1999-10-10'],
                false
            ],
            [
                [Attributes::FIRST_NAME => 'John', Attributes::DATE_OF_BIRTH => '1999-10-10'],
                false
            ],
            [
                [Attributes::FIRST_NAME => 'John', Attributes::SURNAME => 'Smith'],
                false
            ],
            [
                [],
                false
            ],
        ];
    }
}

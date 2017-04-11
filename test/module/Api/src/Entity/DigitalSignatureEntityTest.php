<?php

namespace Dvsa\OlcsTest\Api\Entity;

use Dvsa\OlcsTest\Api\Entity\Abstracts\EntityTester;
use Dvsa\Olcs\Api\Entity\DigitalSignature as Entity;
use Dvsa\Olcs\GdsVerify\Data\Attributes;

/**
 * DigitalSignature Entity Unit Tests
 *
 * Initially auto-generated but won't be overridden
 */
class DigitalSignatureEntityTest extends EntityTester
{
    /**
     * Define the entity to test
     *
     * @var string
     */
    protected $entityClass = Entity::class;

    public function testGetSetAttributes()
    {
        $sut = new Entity();
        $this->assertSame([], $sut->getAttributesArray());
        $sut->setAttributesArray(['foo' => 'bar']);
        $this->assertSame(['foo' => 'bar'], $sut->getAttributesArray());
    }

    /**
     * @dataProvider dpTestGetSignatureName
     */
    public function testGetSignatureName($expected, $attributes)
    {
        $sut = new Entity();
        $sut->setAttributesArray($attributes);
        $this->assertSame($expected, $sut->getSignatureName());
    }

    public function dpTestGetSignatureName()
    {
        return [
            [
                'Bob Smith',
                [
                    Attributes::FIRST_NAME => 'BOB',
                    Attributes::SURNAME=> 'SMITH',
                ],
            ],
            [
                'Bob Smith',
                [
                    Attributes::FIRST_NAME => 'bob',
                    Attributes::SURNAME=> 'smith',
                ],
            ],
            [
                'Bob Smith',
                [
                    Attributes::FIRST_NAME => 'bob',
                    Attributes::SURNAME=> 'smith',
                ],
            ],
            [
                'Smith',
                [
                    Attributes::FIRST_NAME => '',
                    Attributes::SURNAME=> 'smith',
                ],
            ],
            [
                'Bob',
                [
                    Attributes::FIRST_NAME => 'bob',
                    Attributes::SURNAME=> '',
                ],
            ],
            [
                '',
                [],
            ],
        ];
    }

    /**
     * @dataProvider dpTestGetDateOfBirth
     */
    public function testGetDateOfBirth($expected, $attributes)
    {
        $sut = new Entity();
        $sut->setAttributesArray($attributes);
        $this->assertSame($expected, $sut->getDateOfBirth());
    }

    public function dpTestGetDateOfBirth()
    {
        return [
            [null, []],
            ['1942-12-12', [Attributes::DATE_OF_BIRTH => '1942-12-12']],
        ];
    }
}

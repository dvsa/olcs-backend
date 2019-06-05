<?php

namespace Dvsa\OlcsTest\Api\Service\Qa\Structure;

use Dvsa\Olcs\Api\Service\Qa\Structure\Validator;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use RuntimeException;

/**
 * ValidatorTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class ValidatorTest extends MockeryTestCase
{
    private $validator;

    private $rule;

    private $params;

    public function setUp()
    {
        $this->rule = 'Between';

        $this->params = [
            'min' => 0,
            'max' => 47
        ];

        $this->validator = new Validator($this->rule, $this->params);
    }

    public function testGetRepresentation()
    {
        $expectedRepresentation = [
            'rule' => $this->rule,
            'params' => $this->params
        ];

        $this->assertEquals(
            $expectedRepresentation,
            $this->validator->getRepresentation()
        );
    }

    public function testHasRuleTrue()
    {
        $this->assertTrue(
            $this->validator->hasRule($this->rule)
        );
    }

    public function testHasRuleFalse()
    {
        $this->assertFalse(
            $this->validator->hasRule('OtherRule')
        );
    }

    public function testSetParameter()
    {
        $this->validator->setParameter('min', 41);

        $expectedRepresentation = [
            'rule' => $this->rule,
            'params' => [
                'min' => 41,
                'max' => 47
            ]
        ];

        $this->assertEquals(
            $expectedRepresentation,
            $this->validator->getRepresentation()
        );
    }

    public function testSetParameterNotFound()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Parameter foo not found in validator');

        $this->validator->setParameter('foo', 28);
    }
}

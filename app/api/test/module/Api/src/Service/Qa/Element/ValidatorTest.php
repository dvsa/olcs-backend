<?php

namespace Dvsa\OlcsTest\Api\Service\Qa\Element;

use Dvsa\Olcs\Api\Service\Qa\Element\Validator;
use Mockery\Adapter\Phpunit\MockeryTestCase;

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
}

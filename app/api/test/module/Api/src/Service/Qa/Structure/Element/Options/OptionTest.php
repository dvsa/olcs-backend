<?php

namespace Dvsa\OlcsTest\Api\Service\Qa\Structure\Element\Options;

use Dvsa\Olcs\Api\Service\Qa\Structure\Element\Options\Option;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * OptionTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class OptionTest extends MockeryTestCase
{
    private $value;

    private $label;

    public function setUp(): void
    {
        $this->value = '2';
        $this->label = 'Food';
    }

    public function testGetRepresentationWithHint()
    {
        $hint = 'Hint for Food';

        $expectedRepresentation = [
            'value' => $this->value,
            'label' => $this->label,
            'hint' => $hint,
        ];

        $option = new Option($this->value, $this->label, $hint);

        $this->assertEquals(
            $expectedRepresentation,
            $option->getRepresentation()
        );
    }

    public function testGetRepresentationWithoutHint()
    {
        $expectedRepresentation = [
            'value' => $this->value,
            'label' => $this->label,
        ];

        $option = new Option($this->value, $this->label);

        $this->assertEquals(
            $expectedRepresentation,
            $option->getRepresentation()
        );
    }
}

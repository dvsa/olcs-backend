<?php

namespace Dvsa\OlcsTest\Api\Service\Qa\Element;

use Dvsa\Olcs\Api\Service\Qa\Element\TextElement;
use Dvsa\Olcs\Api\Service\Qa\Element\TranslateableText;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * TextElementTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class TextElementTest extends MockeryTestCase
{
    private $hintTranslateableTextRepresentation;

    private $labelTranslateableTextRepresentation;

    private $value;

    private $hintTranslateableText;

    private $labelTranslateableText;

    public function setUp()
    {
        $this->hintTranslateableTextRepresentation = ['hintTranslatelableTextRepresentation'];

        $this->labelTranslateableTextRepresentation = ['labelTranslateableTextRepresentation'];

        $this->value = '123';

        $this->hintTranslateableText = m::mock(TranslateableText::class);
        $this->hintTranslateableText->shouldReceive('getRepresentation')
            ->andReturn($this->hintTranslateableTextRepresentation);

        $this->labelTranslateableText = m::mock(TranslateableText::class);
        $this->labelTranslateableText->shouldReceive('getRepresentation')
            ->andReturn($this->labelTranslateableTextRepresentation);
    }

    public function testGetRepresentationWithHint()
    {
        $textElement = new TextElement(
            $this->labelTranslateableText,
            $this->hintTranslateableText,
            $this->value
        );

        $expectedRepresentation = [
            'label' => $this->labelTranslateableTextRepresentation,
            'hint' => $this->hintTranslateableTextRepresentation,
            'value' => $this->value
        ];

        $this->assertEquals(
            $expectedRepresentation,
            $textElement->getRepresentation()
        );
    }

    public function getRepresentationWithoutHint()
    {
        $textElement = new TextElement(
            $this->labelTranslateableText,
            null,
            $this->value
        );

        $expectedRepresentation = [
            'label' => $this->labelTranslateableTextRepresentation,
            'value' => $this->value
        ];

        $this->assertEquals(
            $expectedRepresentation,
            $textElement->getRepresentation()
        );
    }
}

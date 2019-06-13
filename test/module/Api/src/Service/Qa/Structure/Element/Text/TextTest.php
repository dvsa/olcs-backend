<?php

namespace Dvsa\OlcsTest\Api\Service\Qa\Structure\Element\Text;

use Dvsa\Olcs\Api\Service\Qa\Structure\Element\Text\Text;
use Dvsa\Olcs\Api\Service\Qa\Structure\TranslateableText;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * TextTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class TextTest extends MockeryTestCase
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
        $text = new Text(
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
            $text->getRepresentation()
        );
    }

    public function testGetRepresentationWithoutHint()
    {
        $text = new Text(
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
            $text->getRepresentation()
        );
    }

    public function testGetHint()
    {
        $text = new Text(
            $this->labelTranslateableText,
            $this->hintTranslateableText,
            $this->value
        );

        $this->assertSame(
            $this->hintTranslateableText,
            $text->getHint()
        );
    }
}

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

    public function setUp(): void
    {
        $this->hintTranslateableTextRepresentation = ['hintTranslateableTextRepresentation'];

        $this->labelTranslateableTextRepresentation = ['labelTranslateableTextRepresentation'];

        $this->value = '123';

        $this->hintTranslateableText = m::mock(TranslateableText::class);
        $this->hintTranslateableText->shouldReceive('getRepresentation')
            ->andReturn($this->hintTranslateableTextRepresentation);

        $this->labelTranslateableText = m::mock(TranslateableText::class);
        $this->labelTranslateableText->shouldReceive('getRepresentation')
            ->andReturn($this->labelTranslateableTextRepresentation);
    }

    public function testGetRepresentation()
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

    public function testGetRepresentationWithLabelOnly()
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

    public function testGetRepresentationWithHintOnly()
    {
        $text = new Text(
            null,
            $this->hintTranslateableText,
            $this->value
        );

        $expectedRepresentation = [
            'hint' => $this->hintTranslateableTextRepresentation,
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

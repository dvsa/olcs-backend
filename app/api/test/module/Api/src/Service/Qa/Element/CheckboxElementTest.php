<?php

namespace Dvsa\OlcsTest\Api\Service\Qa\Element;

use Dvsa\Olcs\Api\Service\Qa\Element\CheckboxElement;
use Dvsa\Olcs\Api\Service\Qa\Element\TranslateableText;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * CheckboxElementTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class CheckboxElementTest extends MockeryTestCase
{
    /**
     * @dataProvider dpTestGetRepresentation
     */
    public function testGetRepresentation($checked)
    {
        $labelTranslateableTextRepresentation = ['labelTranslateableTextRepresentation'];

        $labelTranslateableText = m::mock(TranslateableText::class);
        $labelTranslateableText->shouldReceive('getRepresentation')
            ->andReturn($labelTranslateableTextRepresentation);

        $notCheckedMessageTranslateableTextRepresentation = ['notCheckedMessageTranslateableTextRepresentation'];

        $notCheckedMessageTranslateableText = m::mock(TranslateableText::class);
        $notCheckedMessageTranslateableText->shouldReceive('getRepresentation')
            ->andReturn($notCheckedMessageTranslateableTextRepresentation);

        $checkboxElement = new CheckboxElement(
            $labelTranslateableText,
            $notCheckedMessageTranslateableText,
            $checked
        );

        $expectedRepresentation = [
            'label' => $labelTranslateableTextRepresentation,
            'notCheckedMessage' => $notCheckedMessageTranslateableTextRepresentation,
            'checked' => $checked
        ];

        $this->assertEquals(
            $expectedRepresentation,
            $checkboxElement->getRepresentation()
        );
    }

    public function dpTestGetRepresentation()
    {
        return [
            [true],
            [false]
        ];
    }
}

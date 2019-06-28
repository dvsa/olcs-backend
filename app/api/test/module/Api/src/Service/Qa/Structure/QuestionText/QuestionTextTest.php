<?php

namespace Dvsa\OlcsTest\Api\Service\Qa\Structure\QuestionText;

use Dvsa\Olcs\Api\Service\Qa\Structure\FilteredTranslateableText;
use Dvsa\Olcs\Api\Service\Qa\Structure\QuestionText\QuestionText;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * QuestionTextTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class QuestionTextTest extends MockeryTestCase
{
    /**
     * @dataProvider dpTestGenerate
     */
    public function testGenerate(
        $questionFilteredTranslateableText,
        $detailsFilteredTranslateableText,
        $guidanceFilteredTranslateableText,
        $additionalGuidanceFilteredTranslateableText,
        $expectedRepresentation
    ) {
        $questionText = new QuestionText(
            $questionFilteredTranslateableText,
            $detailsFilteredTranslateableText,
            $guidanceFilteredTranslateableText,
            $additionalGuidanceFilteredTranslateableText
        );

        $this->assertEquals(
            $expectedRepresentation,
            $questionText->getRepresentation()
        );
    }

    public function dpTestGenerate()
    {
        $questionRepresentation = ['questionRepresentation'];
        $detailsRepresentation = ['detailsRepresentation'];
        $guidanceRepresentation = ['guidanceRepresentation'];
        $additionalGuidanceRepresentation = ['additionalGuidanceRepresentation'];

        $questionFilteredTranslateableText = m::mock(FilteredTranslateableText::class);
        $questionFilteredTranslateableText->shouldReceive('getRepresentation')
            ->andReturn($questionRepresentation);

        $detailsFilteredTranslateableText = m::mock(FilteredTranslateableText::class);
        $detailsFilteredTranslateableText->shouldReceive('getRepresentation')
            ->andReturn($detailsRepresentation);

        $guidanceFilteredTranslateableText = m::mock(FilteredTranslateableText::class);
        $guidanceFilteredTranslateableText->shouldReceive('getRepresentation')
            ->andReturn($guidanceRepresentation);

        $additionalGuidanceFilteredTranslateableText = m::mock(FilteredTranslateableText::class);
        $additionalGuidanceFilteredTranslateableText->shouldReceive('getRepresentation')
            ->andReturn($additionalGuidanceRepresentation);

        return [
            'All values present' => [
                $questionFilteredTranslateableText,
                $detailsFilteredTranslateableText,
                $guidanceFilteredTranslateableText,
                $additionalGuidanceFilteredTranslateableText,
                [
                    'question' => $questionRepresentation,
                    'details' => $detailsRepresentation,
                    'guidance' => $guidanceRepresentation,
                    'additionalGuidance' => $additionalGuidanceRepresentation
                ]
            ],
            'Some values missing 1' => [
                $questionFilteredTranslateableText,
                $detailsFilteredTranslateableText,
                null,
                null,
                [
                    'question' => $questionRepresentation,
                    'details' => $detailsRepresentation,
                ]
            ],
            'All values present' => [
                null,
                null,
                $guidanceFilteredTranslateableText,
                $additionalGuidanceFilteredTranslateableText,
                [
                    'guidance' => $guidanceRepresentation,
                    'additionalGuidance' => $additionalGuidanceRepresentation
                ]
            ],
        ];
    }

    public function testGetGuidance()
    {
        $guidanceFilteredTranslateableText = m::mock(FilteredTranslateableText::class);

        $questionText = new QuestionText(
            m::mock(FilteredTranslateableText::class),
            m::mock(FilteredTranslateableText::class),
            $guidanceFilteredTranslateableText,
            m::mock(FilteredTranslateableText::class)
        );

        $this->assertSame(
            $guidanceFilteredTranslateableText,
            $questionText->getGuidance()
        );
    }
}

<?php

namespace Dvsa\OlcsTest\Api\Service\Qa\Element;

use Dvsa\Olcs\Api\Service\Qa\Element\FilteredTranslateableText;
use Dvsa\Olcs\Api\Service\Qa\Element\FilteredTranslateableTextFactory;
use Dvsa\Olcs\Api\Service\Qa\Element\FilteredTranslateableTextGenerator;
use Dvsa\Olcs\Api\Service\Qa\Element\TranslateableText;
use Dvsa\Olcs\Api\Service\Qa\Element\TranslateableTextGenerator;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * FilteredTranslateableTextGeneratorTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class FilteredTranslateableTextGeneratorTest extends MockeryTestCase
{
    public function testGenerate()
    {
        $optionFilter = 'htmlEscape';

        $optionTranslateableText = [
            'key' => 'translateableTextKey',
            'parameters' => [
                'translateableTextParameter1',
                'translateableTextParameter2'
            ]
        ];

        $options = [
            'filter' => $optionFilter,
            'translateableText' => $optionTranslateableText
        ];

        $translateableText = m::mock(TranslateableText::class);

        $filteredTranslateableText = m::mock(FilteredTranslateableText::class);

        $filteredTranslateableTextFactory = m::mock(FilteredTranslateableTextFactory::class);
        $filteredTranslateableTextFactory->shouldReceive('create')
            ->with($optionFilter, $translateableText)
            ->andReturn($filteredTranslateableText);

        $translateableTextGenerator = m::mock(TranslateableTextGenerator::class);
        $translateableTextGenerator->shouldReceive('generate')
            ->with($optionTranslateableText)
            ->andReturn($translateableText);

        $filteredTranslateableTextGenerator = new FilteredTranslateableTextGenerator(
            $filteredTranslateableTextFactory,
            $translateableTextGenerator
        );

        $this->assertSame(
            $filteredTranslateableText,
            $filteredTranslateableTextGenerator->generate($options)
        );
    }
}

<?php

namespace Dvsa\OlcsTest\Api\Service\Qa\Structure\Element\Radio;

use Dvsa\Olcs\Api\Entity\Generic\ApplicationStep as ApplicationStepEntity;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\Options\OptionsGenerator;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\Radio\Radio;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\Radio\RadioFactory;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\Radio\RadioGenerator;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\ElementGeneratorContext;
use Dvsa\Olcs\Api\Service\Qa\Structure\TranslateableText;
use Dvsa\Olcs\Api\Service\Qa\Structure\TranslateableTextGenerator;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * RadioGeneratorTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class RadioGeneratorTest extends MockeryTestCase
{
    public function testGenerate()
    {
        $answerValue = 'permit_app_uc';

        $notSelectedMessageOptions = [
            'key' => 'notSelectedMessageKey',
            'parameters' => [
                'notSelectedMessageParameter1',
                'notSelectedMessageParameter2'
            ]
        ];

        $sourceOptions = [
            'name' => 'direct',
            'options' => [
                'key1' => 'value1',
                'key2' => 'value2'
            ]
        ];

        $returnedOptions = [
            'permit_app_uc' => 'Under Consideration',
            'permit_app_nys' => 'Not Yet Submitted'
        ];

        $options = [
            'notSelectedMessage' => $notSelectedMessageOptions,
            'source' => $sourceOptions
        ];

        $notSelectedMessageTranslateableText = m::mock(TranslateableText::class);

        $radio = m::mock(Radio::class);

        $radioFactory = m::mock(RadioFactory::class);
        $radioFactory->shouldReceive('create')
            ->with($returnedOptions, $notSelectedMessageTranslateableText, $answerValue)
            ->andReturn($radio);

        $applicationStepEntity = m::mock(ApplicationStepEntity::class);
        $applicationStepEntity->shouldReceive('getDecodedOptionSource')
            ->andReturn($options);

        $elementGeneratorContext = m::mock(ElementGeneratorContext::class);
        $elementGeneratorContext->shouldReceive('getApplicationStepEntity')
            ->andReturn($applicationStepEntity);
        $elementGeneratorContext->shouldReceive('getAnswerValue')
            ->withNoArgs()
            ->andReturn($answerValue);

        $optionsGenerator = m::mock(OptionsGenerator::class);
        $optionsGenerator->shouldReceive('generate')
            ->with($sourceOptions)
            ->andReturn($returnedOptions);

        $translateableTextGenerator = m::mock(TranslateableTextGenerator::class);
        $translateableTextGenerator->shouldReceive('generate')
            ->with($notSelectedMessageOptions)
            ->andReturn($notSelectedMessageTranslateableText);

        $radioGenerator = new RadioGenerator($radioFactory, $optionsGenerator, $translateableTextGenerator);

        $this->assertSame(
            $radio,
            $radioGenerator->generate($elementGeneratorContext)
        );
    }
}

<?php

namespace Dvsa\OlcsTest\Api\Service\Qa\Element;

use Dvsa\Olcs\Api\Entity\Generic\Answer as AnswerEntity;
use Dvsa\Olcs\Api\Entity\Generic\ApplicationStep as ApplicationStepEntity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication as IrhpApplicationEntity;
use Dvsa\Olcs\Api\Service\Qa\Element\CheckboxElement;
use Dvsa\Olcs\Api\Service\Qa\Element\CheckboxElementFactory;
use Dvsa\Olcs\Api\Service\Qa\Element\CheckboxElementGenerator;
use Dvsa\Olcs\Api\Service\Qa\Element\TranslateableText;
use Dvsa\Olcs\Api\Service\Qa\Element\TranslateableTextGenerator;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * CheckboxElementGeneratorTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class CheckboxElementGeneratorTest extends MockeryTestCase
{
    /**
     * @dataProvider dpTestGenerate
     */
    public function testGenerate($answerEntity, $expectedAnswerValue)
    {
        $labelOptions = [
            'key' => 'labelKey',
            'parameters' => [
                'labelParameter1',
                'labelParameter2'
            ]
        ];

        $notCheckedMessageOptions = [
            'key' => 'notCheckedMessageKey',
            'parameters' => [
                'notCheckedMessageParameter1',
                'notCheckedMessageParameter2'
            ]
        ];

        $options = [
            'label' => $labelOptions,
            'notCheckedMessage' => $notCheckedMessageOptions
        ];

        $labelTranslateableText = m::mock(TranslateableText::class);

        $notCheckedMessageTranslateableText = m::mock(TranslateableText::class);

        $checkboxElement = m::mock(CheckboxElement::class);

        $checkboxElementFactory = m::mock(CheckboxElementFactory::class);
        $checkboxElementFactory->shouldReceive('create')
            ->with($labelTranslateableText, $notCheckedMessageTranslateableText, $expectedAnswerValue)
            ->andReturn($checkboxElement);

        $applicationStepEntity = m::mock(ApplicationStepEntity::class);
        $applicationStepEntity->shouldReceive('getDecodedOptionSource')
            ->andReturn($options);

        $irhpApplicationEntity = m::mock(IrhpApplicationEntity::class);

        $translateableTextGenerator = m::mock(TranslateableTextGenerator::class);
        $translateableTextGenerator->shouldReceive('generate')
            ->with($labelOptions)
            ->andReturn($labelTranslateableText);
        $translateableTextGenerator->shouldReceive('generate')
            ->with($notCheckedMessageOptions)
            ->andReturn($notCheckedMessageTranslateableText);

        $checkboxElementGenerator = new CheckboxElementGenerator($checkboxElementFactory, $translateableTextGenerator);

        $this->assertSame(
            $checkboxElement,
            $checkboxElementGenerator->generate($applicationStepEntity, $irhpApplicationEntity, $answerEntity)
        );
    }

    public function dpTestGenerate()
    {
        $answerEntityWithTrueValue = m::mock(AnswerEntity::class);
        $answerEntityWithTrueValue->shouldReceive('getValue')
            ->andReturn(true);

        $answerEntityWithFalseValue = m::mock(AnswerEntity::class);
        $answerEntityWithFalseValue->shouldReceive('getValue')
            ->andReturn(false);

        return [
            [null, false],
            [$answerEntityWithFalseValue, false],
            [$answerEntityWithTrueValue, true],
        ];
    }
}

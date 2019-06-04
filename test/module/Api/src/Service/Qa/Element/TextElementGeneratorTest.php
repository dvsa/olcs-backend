<?php

namespace Dvsa\OlcsTest\Api\Service\Qa\Element;

use Dvsa\Olcs\Api\Entity\Generic\Answer as AnswerEntity;
use Dvsa\Olcs\Api\Entity\Generic\ApplicationStep as ApplicationStepEntity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication as IrhpApplicationEntity;
use Dvsa\Olcs\Api\Service\Qa\Element\TextElement;
use Dvsa\Olcs\Api\Service\Qa\Element\TextElementFactory;
use Dvsa\Olcs\Api\Service\Qa\Element\TextElementGenerator;
use Dvsa\Olcs\Api\Service\Qa\Element\TranslateableText;
use Dvsa\Olcs\Api\Service\Qa\Element\TranslateableTextGenerator;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * TextElementGeneratorTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class TextElementGeneratorTest extends MockeryTestCase
{
    public function setUp()
    {
        $this->hintOption = [
            'key' => 'hintKey',
            'parameters' => [
                'hintParameter1',
                'hintParameter2'
            ]
        ];

        $this->labelOption = [
            'key' => 'labelKey',
            'parameters' => [
                'labelParameter1',
                'labelParameter2'
            ]
        ];

        $this->answerEntity = m::mock(AnswerEntity::class);

        $this->applicationStepEntity = m::mock(ApplicationStepEntity::class);

        $this->irhpApplicationEntity = m::mock(IrhpApplicationEntity::class);

        $this->hintTranslateableText = m::mock(TranslateableText::class);

        $this->labelTranslateableText = m::mock(TranslateableText::class);

        $this->textElement = m::mock(TextElement::class);

        $this->textElementFactory = m::mock(TextElementFactory::class);

        $this->translateableTextGenerator = m::mock(TranslateableTextGenerator::class);
        $this->translateableTextGenerator->shouldReceive('generate')
            ->with($this->hintOption)
            ->andReturn($this->hintTranslateableText);
        $this->translateableTextGenerator->shouldReceive('generate')
            ->with($this->labelOption)
            ->andReturn($this->labelTranslateableText);
    }

    public function testGenerateWithAnswerAndHint()
    {
        $answerValue = '456';

        $this->answerEntity->shouldReceive('getValue')
            ->andReturn($answerValue);

        $this->textElementFactory->shouldReceive('create')
            ->with($this->labelTranslateableText, $this->hintTranslateableText, $answerValue)
            ->andReturn($this->textElement);

        $decodedOptionSource = [
            'hint' => $this->hintOption,
            'label' => $this->labelOption
        ];

        $this->applicationStepEntity->shouldReceive('getDecodedOptionSource')
            ->andReturn($decodedOptionSource);

        $textElementGenerator = new TextElementGenerator(
            $this->textElementFactory,
            $this->translateableTextGenerator
        );

        $this->assertSame(
            $this->textElement,
            $textElementGenerator->generate(
                $this->applicationStepEntity,
                $this->irhpApplicationEntity,
                $this->answerEntity
            )
        );
    }

    public function testGenerateWithNoAnswerAndHint()
    {
        $answerValue = null;

        $this->textElementFactory->shouldReceive('create')
            ->with($this->labelTranslateableText, $this->hintTranslateableText, $answerValue)
            ->andReturn($this->textElement);

        $decodedOptionSource = [
            'hint' => $this->hintOption,
            'label' => $this->labelOption
        ];

        $this->applicationStepEntity->shouldReceive('getDecodedOptionSource')
            ->andReturn($decodedOptionSource);

        $textElementGenerator = new TextElementGenerator(
            $this->textElementFactory,
            $this->translateableTextGenerator
        );

        $this->assertSame(
            $this->textElement,
            $textElementGenerator->generate(
                $this->applicationStepEntity,
                $this->irhpApplicationEntity,
                null
            )
        );
    }

    public function testGenerateWithAnswerAndNoHint()
    {
        $answerValue = '456';

        $this->answerEntity->shouldReceive('getValue')
            ->andReturn($answerValue);

        $this->textElementFactory->shouldReceive('create')
            ->with($this->labelTranslateableText, null, $answerValue)
            ->andReturn($this->textElement);

        $decodedOptionSource = [
            'label' => $this->labelOption
        ];

        $this->applicationStepEntity->shouldReceive('getDecodedOptionSource')
            ->andReturn($decodedOptionSource);

        $textElementGenerator = new TextElementGenerator(
            $this->textElementFactory,
            $this->translateableTextGenerator
        );

        $this->assertSame(
            $this->textElement,
            $textElementGenerator->generate(
                $this->applicationStepEntity,
                $this->irhpApplicationEntity,
                $this->answerEntity
            )
        );
    }

    public function testGenerateWithNoAnswerAndNoHint()
    {
        $answerValue = null;

        $this->textElementFactory->shouldReceive('create')
            ->with($this->labelTranslateableText, null, $answerValue)
            ->andReturn($this->textElement);

        $decodedOptionSource = [
            'label' => $this->labelOption
        ];

        $this->applicationStepEntity->shouldReceive('getDecodedOptionSource')
            ->andReturn($decodedOptionSource);

        $textElementGenerator = new TextElementGenerator(
            $this->textElementFactory,
            $this->translateableTextGenerator
        );

        $this->assertSame(
            $this->textElement,
            $textElementGenerator->generate(
                $this->applicationStepEntity,
                $this->irhpApplicationEntity,
                null
            )
        );
    }
}

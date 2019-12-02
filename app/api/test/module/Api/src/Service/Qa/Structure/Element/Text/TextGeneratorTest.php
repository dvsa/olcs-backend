<?php

namespace Dvsa\OlcsTest\Api\Service\Qa\Structure\Element\Text;

use Dvsa\Olcs\Api\Entity\Generic\ApplicationStep as ApplicationStepEntity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication as IrhpApplicationEntity;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\ElementGeneratorContext;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\Text\Text;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\Text\TextFactory;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\Text\TextGenerator;
use Dvsa\Olcs\Api\Service\Qa\Structure\TranslateableText;
use Dvsa\Olcs\Api\Service\Qa\Structure\TranslateableTextGenerator;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * TextGeneratorTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class TextGeneratorTest extends MockeryTestCase
{
    private $answerValue;

    private $applicationStepEntity;

    private $irhpApplicationEntity;

    private $elementGeneratorContext;

    private $labelTranslateableText;

    private $text;

    private $textFactory;

    private $translateableTextGenerator;

    private $textGenerator;

    public function setUp()
    {
        $this->answerValue = '456';

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
        $this->irhpApplicationEntity->shouldReceive('getAnswer')
            ->with($this->applicationStepEntity)
            ->andReturn($this->answerValue);

        $this->elementGeneratorContext = m::mock(ElementGeneratorContext::class);
        $this->elementGeneratorContext->shouldReceive('getApplicationStepEntity')
            ->andReturn($this->applicationStepEntity);
        $this->elementGeneratorContext->shouldReceive('getIrhpApplicationEntity')
            ->andReturn($this->irhpApplicationEntity);

        $this->labelTranslateableText = m::mock(TranslateableText::class);

        $this->text = m::mock(Text::class);

        $this->textFactory = m::mock(TextFactory::class);

        $this->translateableTextGenerator = m::mock(TranslateableTextGenerator::class);
        $this->translateableTextGenerator->shouldReceive('generate')
            ->with($this->labelOption)
            ->andReturn($this->labelTranslateableText);

        $this->textGenerator = new TextGenerator(
            $this->textFactory,
            $this->translateableTextGenerator
        );
    }

    public function testGenerateWithHint()
    {
        $hintTranslateableText = m::mock(TranslateableText::class);

        $this->translateableTextGenerator->shouldReceive('generate')
            ->with($this->hintOption)
            ->andReturn($hintTranslateableText);

        $this->textFactory->shouldReceive('create')
            ->with($this->labelTranslateableText, $hintTranslateableText, $this->answerValue)
            ->andReturn($this->text);

        $decodedOptionSource = [
            'hint' => $this->hintOption,
            'label' => $this->labelOption
        ];

        $this->applicationStepEntity->shouldReceive('getDecodedOptionSource')
            ->andReturn($decodedOptionSource);

        $this->assertSame(
            $this->text,
            $this->textGenerator->generate($this->elementGeneratorContext)
        );
    }

    public function testGenerateWithNoHint()
    {
        $this->textFactory->shouldReceive('create')
            ->with($this->labelTranslateableText, null, $this->answerValue)
            ->andReturn($this->text);

        $decodedOptionSource = [
            'label' => $this->labelOption
        ];

        $this->applicationStepEntity->shouldReceive('getDecodedOptionSource')
            ->andReturn($decodedOptionSource);

        $this->assertSame(
            $this->text,
            $this->textGenerator->generate($this->elementGeneratorContext)
        );
    }
}

<?php

namespace Dvsa\OlcsTest\Api\Service\Qa\Structure\QuestionText\Custom\EcmtShortTerm;

use Dvsa\Olcs\Api\Service\Qa\Structure\QuestionText\Custom\EcmtShortTerm\PermitUsageGenerator;
use Dvsa\Olcs\Api\Service\Qa\Structure\QuestionText\QuestionTextGenerator;
use Dvsa\Olcs\Api\Service\Qa\Structure\QuestionText\QuestionTextGeneratorContext;
use Dvsa\Olcs\Api\Service\Qa\Structure\TranslateableText;
use Dvsa\Olcs\Api\Service\Qa\Structure\QuestionText\QuestionText;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * PermitUsageGeneratorTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class PermitUsageGeneratorTest extends MockeryTestCase
{
    private $questionText;

    private $questionTextGeneratorContext;

    private $permitUsageGenerator;

    public function setUp()
    {
        $this->questionText = m::mock(QuestionText::class);

        $this->questionTextGeneratorContext = m::mock(QuestionTextGeneratorContext::class);

        $questionTextGenerator = m::mock(QuestionTextGenerator::class);
        $questionTextGenerator->shouldReceive('generate')
            ->with($this->questionTextGeneratorContext)
            ->andReturn($this->questionText);

        $this->permitUsageGenerator = new PermitUsageGenerator($questionTextGenerator);
    }

    public function testGenerate2019()
    {
        $additionalGuidanceTranslateableText = m::mock(TranslateableText::class);
        $additionalGuidanceTranslateableText->shouldReceive('setKey')
            ->with('qanda.ecmt-short-term.permit-usage.additional-guidance.2019')
            ->once();

        $this->questionText->shouldReceive('getAdditionalGuidance->getTranslateableText')
            ->andReturn($additionalGuidanceTranslateableText);

        $this->questionTextGeneratorContext->shouldReceive(
            'getIrhpApplicationEntity->getFirstIrhpPermitApplication' .
            '->getIrhpPermitWindow->getIrhpPermitStock->getValidityYear'
        )->andReturn(2019);

        $this->assertSame(
            $this->questionText,
            $this->permitUsageGenerator->generate($this->questionTextGeneratorContext)
        );
    }

    /**
     * @dataProvider dpTestGenerateNot2019
     */
    public function testGenerateNot2019($year)
    {
        $this->questionText->shouldReceive('getAdditionalGuidance')
            ->never();

        $this->questionTextGeneratorContext->shouldReceive(
            'getIrhpApplicationEntity->getFirstIrhpPermitApplication' .
            '->getIrhpPermitWindow->getIrhpPermitStock->getValidityYear'
        )->andReturn($year);

        $this->assertSame(
            $this->questionText,
            $this->permitUsageGenerator->generate($this->questionTextGeneratorContext)
        );
    }

    public function dpTestGenerateNot2019()
    {
        return [
            [2018],
            [2020]
        ];
    }
}

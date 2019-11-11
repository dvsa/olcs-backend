<?php

namespace Dvsa\OlcsTest\Api\Service\Qa\Structure\QuestionText\Custom\EcmtShortTerm;

use Dvsa\Olcs\Api\Entity\Generic\ApplicationPathGroup as ApplicationPathGroupEntity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication as IrhpApplicationEntity;
use Dvsa\Olcs\Api\Service\Qa\Structure\QuestionText\Custom\EcmtShortTerm\RestrictedCountriesGenerator;
use Dvsa\Olcs\Api\Service\Qa\Structure\QuestionText\QuestionTextGenerator;
use Dvsa\Olcs\Api\Service\Qa\Structure\QuestionText\QuestionTextGeneratorContext;
use Dvsa\Olcs\Api\Service\Qa\Structure\QuestionText\QuestionText;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * RestrictedCountriesGeneratorTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class RestrictedCountriesGeneratorTest extends MockeryTestCase
{
    public function testGenerateUpdateKey()
    {
        $irhpApplicationEntity = m::mock(IrhpApplicationEntity::class);
        $irhpApplicationEntity->shouldReceive('getAssociatedStock->getApplicationPathGroup->getId')
            ->withNoArgs()
            ->andReturn(ApplicationPathGroupEntity::ECMT_SHORT_TERM_2020_APSG_WITHOUT_SECTORS_ID);

        $questionTranslateableText = m::mock(TranslateableText::class);
        $questionTranslateableText->shouldReceive('setKey')
            ->with('qanda.ecmt-short-term.restricted-countries.question.ecmt-short-term-2020-apsg-without-sectors')
            ->once();

        $questionText = m::mock(QuestionText::class);
        $questionText->shouldReceive('getQuestion->getTranslateableText')
            ->andReturn($questionTranslateableText);

        $questionTextGeneratorContext = m::mock(QuestionTextGeneratorContext::class);
        $questionTextGeneratorContext->shouldReceive('getIrhpApplicationEntity')
            ->andReturn($irhpApplicationEntity);

        $questionTextGenerator = m::mock(QuestionTextGenerator::class);
        $questionTextGenerator->shouldReceive('generate')
            ->with($questionTextGeneratorContext)
            ->andReturn($questionText);

        $restrictedCountriesGenerator = new RestrictedCountriesGenerator($questionTextGenerator);

        $this->assertSame(
            $questionText,
            $restrictedCountriesGenerator->generate($questionTextGeneratorContext)
        );
    }

    /**
     * @dataProvider dpGenerateNoChanges
     */
    public function testGenerateNoChanges($applicationPathGroupId)
    {
        $irhpApplicationEntity = m::mock(IrhpApplicationEntity::class);
        $irhpApplicationEntity->shouldReceive('getAssociatedStock->getApplicationPathGroup->getId')
            ->withNoArgs()
            ->andReturn($applicationPathGroupId);

        $questionText = m::mock(QuestionText::class);
        $questionText->shouldReceive('getQuestion')
            ->never();

        $questionTextGeneratorContext = m::mock(QuestionTextGeneratorContext::class);
        $questionTextGeneratorContext->shouldReceive('getIrhpApplicationEntity')
            ->andReturn($irhpApplicationEntity);

        $questionTextGenerator = m::mock(QuestionTextGenerator::class);
        $questionTextGenerator->shouldReceive('generate')
            ->with($questionTextGeneratorContext)
            ->andReturn($questionText);

        $restrictedCountriesGenerator = new RestrictedCountriesGenerator($questionTextGenerator);

        $this->assertSame(
            $questionText,
            $restrictedCountriesGenerator->generate($questionTextGeneratorContext)
        );
    }

    public function dpGenerateNoChanges()
    {
        return [
            [ApplicationPathGroupEntity::ECMT_SHORT_TERM_2020_APSG_WITH_SECTORS_ID],
            [ApplicationPathGroupEntity::ECMT_SHORT_TERM_2020_APGG],
        ];
    }
}

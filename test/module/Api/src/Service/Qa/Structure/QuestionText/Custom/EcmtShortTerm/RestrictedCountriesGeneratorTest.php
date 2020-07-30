<?php

namespace Dvsa\OlcsTest\Api\Service\Qa\Structure\QuestionText\Custom\EcmtShortTerm;

use Dvsa\Olcs\Api\Entity\Generic\ApplicationPathGroup as ApplicationPathGroupEntity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication as IrhpApplicationEntity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitType as IrhpPermitTypeEntity;
use Dvsa\Olcs\Api\Service\Qa\QaContext;
use Dvsa\Olcs\Api\Service\Qa\Structure\QuestionText\Custom\EcmtShortTerm\RestrictedCountriesGenerator;
use Dvsa\Olcs\Api\Service\Qa\Structure\QuestionText\QuestionTextGenerator;
use Dvsa\Olcs\Api\Service\Qa\Structure\QuestionText\QuestionText;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use RuntimeException;

/**
 * RestrictedCountriesGeneratorTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class RestrictedCountriesGeneratorTest extends MockeryTestCase
{
    const ECMT_ANNUAL_APP_PATH_GROUP_ID = 8;

    private $irhpApplicationEntity;

    private $qaContext;

    private $questionTextGenerator;

    private $restrictedCountriesGenerator;

    public function setUp(): void
    {
        $this->irhpApplicationEntity = m::mock(IrhpApplicationEntity::class);

        $this->qaContext = m::mock(QaContext::class);
        $this->qaContext->shouldReceive('getQaEntity')
            ->withNoArgs()
            ->andReturn($this->irhpApplicationEntity);

        $this->questionTextGenerator = m::mock(QuestionTextGenerator::class);

        $this->restrictedCountriesGenerator = new RestrictedCountriesGenerator($this->questionTextGenerator);
    }

    /**
     * @dataProvider dpGenerate
     */
    public function testGenerate(
        $irhpPermitTypeId,
        $applicationPathGroupId,
        $expectedQuestionKey,
        $expectedQuestionSummaryKey,
        $expectedGuidanceKey
    ) {
        $this->irhpApplicationEntity->shouldReceive('getIrhpPermitType->getId')
            ->withNoArgs()
            ->andReturn($irhpPermitTypeId);
        $this->irhpApplicationEntity->shouldReceive('getAssociatedStock->getApplicationPathGroup->getId')
            ->withNoArgs()
            ->andReturn($applicationPathGroupId);

        $questionTranslateableText = m::mock(TranslateableText::class);
        $questionTranslateableText->shouldReceive('setKey')
            ->with($expectedQuestionKey)
            ->once();

        $questionSummaryTranslateableText = m::mock(TranslateableText::class);
        $questionSummaryTranslateableText->shouldReceive('setKey')
            ->with($expectedQuestionSummaryKey)
            ->once();

        $guidanceTranslateableText = m::mock(TranslateableText::class);
        $guidanceTranslateableText->shouldReceive('setKey')
            ->with($expectedGuidanceKey)
            ->once();

        $questionText = m::mock(QuestionText::class);
        $questionText->shouldReceive('getQuestion->getTranslateableText')
            ->withNoArgs()
            ->andReturn($questionTranslateableText);
        $questionText->shouldReceive('getQuestionSummary->getTranslateableText')
            ->withNoArgs()
            ->andReturn($questionSummaryTranslateableText);
        $questionText->shouldReceive('getGuidance->getTranslateableText')
            ->withNoArgs()
            ->andReturn($guidanceTranslateableText);

        $this->questionTextGenerator->shouldReceive('generate')
            ->with($this->qaContext)
            ->andReturn($questionText);

        $this->assertSame(
            $questionText,
            $this->restrictedCountriesGenerator->generate($this->qaContext)
        );
    }

    public function dpGenerate()
    {
        return [
            [
                IrhpPermitTypeEntity::IRHP_PERMIT_TYPE_ID_ECMT,
                self::ECMT_ANNUAL_APP_PATH_GROUP_ID,
                'qanda.ecmt-annual.restricted-countries.question',
                'qanda.ecmt-annual.restricted-countries.question-summary',
                'qanda.ecmt-annual.restricted-countries.guidance',
            ],
            [
                IrhpPermitTypeEntity::IRHP_PERMIT_TYPE_ID_ECMT_SHORT_TERM,
                ApplicationPathGroupEntity::ECMT_SHORT_TERM_2020_APSG_WITHOUT_SECTORS_ID,
                'qanda.ecmt-short-term.restricted-countries.question.ecmt-short-term-2020-apsg-without-sectors',
                'qanda.ecmt-short-term.restricted-countries.question-summary.ecmt-short-term-2020-apsg-without-sectors',
                'qanda.ecmt-short-term.restricted-countries.guidance',
            ],
            [
                IrhpPermitTypeEntity::IRHP_PERMIT_TYPE_ID_ECMT_SHORT_TERM,
                ApplicationPathGroupEntity::ECMT_SHORT_TERM_2020_APSG_WITH_SECTORS_ID,
                'qanda.ecmt-short-term.restricted-countries.question',
                'qanda.ecmt-short-term.restricted-countries.question-summary',
                'qanda.ecmt-short-term.restricted-countries.guidance',
            ],
            [
                IrhpPermitTypeEntity::IRHP_PERMIT_TYPE_ID_ECMT_SHORT_TERM,
                ApplicationPathGroupEntity::ECMT_SHORT_TERM_2020_APGG,
                'qanda.ecmt-short-term.restricted-countries.question',
                'qanda.ecmt-short-term.restricted-countries.question-summary',
                'qanda.ecmt-short-term.restricted-countries.guidance',
            ],
        ];
    }

    /**
     * @dataProvider dpGenerateExceptionOnUnsupportedType
     */
    public function testGenerateExceptionOnUnsupportedType($irhpPermitTypeId)
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('This question does not support permit type ' . $irhpPermitTypeId);

        $this->irhpApplicationEntity->shouldReceive('getIrhpPermitType->getId')
            ->withNoArgs()
            ->andReturn($irhpPermitTypeId);

        $this->restrictedCountriesGenerator->generate($this->qaContext);
    }

    public function dpGenerateExceptionOnUnsupportedType()
    {
        return [
            [IrhpPermitTypeEntity::IRHP_PERMIT_TYPE_ID_ECMT_REMOVAL],
            [IrhpPermitTypeEntity::IRHP_PERMIT_TYPE_ID_BILATERAL],
            [IrhpPermitTypeEntity::IRHP_PERMIT_TYPE_ID_MULTILATERAL],
            [IrhpPermitTypeEntity::IRHP_PERMIT_TYPE_ID_CERT_ROADWORTHINESS_VEHICLE],
            [IrhpPermitTypeEntity::IRHP_PERMIT_TYPE_ID_CERT_ROADWORTHINESS_TRAILER],
        ];
    }
}

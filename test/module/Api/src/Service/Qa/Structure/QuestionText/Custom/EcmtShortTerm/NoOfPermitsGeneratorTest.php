<?php

namespace Dvsa\OlcsTest\Api\Service\Qa\Structure\QuestionText\Custom\EcmtShortTerm;

use Dvsa\Olcs\Api\Service\Qa\Structure\QuestionText\Custom\EcmtShortTerm\NoOfPermitsGenerator;
use Dvsa\Olcs\Api\Service\Qa\Structure\QuestionText\QuestionTextGenerator;
use Dvsa\Olcs\Api\Service\Qa\Structure\QuestionText\QuestionTextGeneratorContext;
use Dvsa\Olcs\Api\Domain\Repository\FeeType as FeeTypeRepository;
use Dvsa\Olcs\Api\Entity\Fee\FeeType as FeeTypeEntity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication as IrhpApplicationEntity;
use Dvsa\Olcs\Api\Service\Qa\Structure\QuestionText\QuestionText;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * NoOfPermitsGeneratorTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class NoOfPermitsGeneratorTest extends MockeryTestCase
{
    public function testGenerate()
    {
        $applicationFeePerPermit = '15.00';
        $issueFeePerPermit = '5.00';

        $applicationFeeTypeEntity = m::mock(FeeTypeEntity::class);
        $applicationFeeTypeEntity->shouldReceive('getFixedValue')
            ->andReturn($applicationFeePerPermit);

        $issueFeeTypeEntity = m::mock(FeeTypeEntity::class);
        $issueFeeTypeEntity->shouldReceive('getFixedValue')
            ->andReturn($issueFeePerPermit);

        $irhpApplicationEntity = m::mock(IrhpApplicationEntity::class);
        $irhpApplicationEntity->shouldReceive('getApplicationFeeProductReference')
            ->andReturn(FeeTypeEntity::FEE_TYPE_ECMT_APP_PRODUCT_REF);
        $irhpApplicationEntity->shouldReceive('getIssueFeeProductReference')
            ->andReturn(FeeTypeEntity::FEE_TYPE_ECMT_SHORT_TERM_ISSUE_PRODUCT_REF);

        $additionalGuidanceTranslateableTextParameterIndex0 = m::mock(TranslateableTextParameter::class);
        $additionalGuidanceTranslateableTextParameterIndex0->shouldReceive('setValue')
            ->with($applicationFeePerPermit)
            ->once();

        $additionalGuidanceTranslateableTextParameterIndex1 = m::mock(TranslateableTextParameter::class);
        $additionalGuidanceTranslateableTextParameterIndex1->shouldReceive('setValue')
            ->with($issueFeePerPermit)
            ->once();

        $additionalGuidanceTranslateableText = m::mock(TranslateableText::class);
        $additionalGuidanceTranslateableText->shouldReceive('getParameter')
            ->with(0)
            ->andReturn($additionalGuidanceTranslateableTextParameterIndex0);
        $additionalGuidanceTranslateableText->shouldReceive('getParameter')
            ->with(1)
            ->andReturn($additionalGuidanceTranslateableTextParameterIndex1);

        $questionText = m::mock(QuestionText::class);
        $questionText->shouldReceive('getAdditionalGuidance->getTranslateableText')
            ->andReturn($additionalGuidanceTranslateableText);

        $questionTextGeneratorContext = m::mock(QuestionTextGeneratorContext::class);
        $questionTextGeneratorContext->shouldReceive('getIrhpApplicationEntity')
            ->andReturn($irhpApplicationEntity);

        $questionTextGenerator = m::mock(QuestionTextGenerator::class);
        $questionTextGenerator->shouldReceive('generate')
            ->with($questionTextGeneratorContext)
            ->andReturn($questionText);

        $feeTypeRepo = m::mock(FeeTypeRepository::class);
        $feeTypeRepo->shouldReceive('getLatestByProductReference')
            ->with(FeeTypeEntity::FEE_TYPE_ECMT_APP_PRODUCT_REF)
            ->andReturn($applicationFeeTypeEntity);
        $feeTypeRepo->shouldReceive('getLatestByProductReference')
            ->with(FeeTypeEntity::FEE_TYPE_ECMT_SHORT_TERM_ISSUE_PRODUCT_REF)
            ->andReturn($issueFeeTypeEntity);

        $noOfPermitsGenerator = new NoOfPermitsGenerator(
            $questionTextGenerator,
            $feeTypeRepo
        );

        $this->assertSame(
            $questionText,
            $noOfPermitsGenerator->generate($questionTextGeneratorContext)
        );
    }
}

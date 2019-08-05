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
        $totalFeePerPermit = '20.00';
        $applicationFeePerPermit = '15.00';
        $issueFeePerPermit = '5.00';

        $applicationFeeTypeEntity = m::mock(FeeTypeEntity::class);
        $applicationFeeTypeEntity->shouldReceive('getFixedValue')
            ->andReturn($applicationFeePerPermit);

        $issueFeeTypeEntity = m::mock(FeeTypeEntity::class);
        $issueFeeTypeEntity->shouldReceive('getFixedValue')
            ->andReturn($issueFeePerPermit);

        $irhpApplicationEntity = m::mock(IrhpApplicationEntity::class);
        $irhpApplicationEntity->shouldReceive('getFeePerPermit')
            ->with($applicationFeeTypeEntity, $issueFeeTypeEntity)
            ->andReturn($totalFeePerPermit);
        $irhpApplicationEntity->shouldReceive('getApplicationFeeProductReference')
            ->andReturn(FeeTypeEntity::FEE_TYPE_ECMT_APP_PRODUCT_REF);
        $irhpApplicationEntity->shouldReceive('getIssueFeeProductReference')
            ->andReturn(FeeTypeEntity::FEE_TYPE_ECMT_SHORT_TERM_ISSUE_PRODUCT_REF);

        $guidanceTranslateableTextParameterIndex0 = m::mock(TranslateableTextParameter::class);
        $guidanceTranslateableTextParameterIndex0->shouldReceive('setValue')
            ->with($totalFeePerPermit)
            ->once();

        $guidanceTranslateableTextParameterIndex1 = m::mock(TranslateableTextParameter::class);
        $guidanceTranslateableTextParameterIndex1->shouldReceive('setValue')
            ->with($applicationFeePerPermit)
            ->once();

        $guidanceTranslateableTextParameterIndex2 = m::mock(TranslateableTextParameter::class);
        $guidanceTranslateableTextParameterIndex2->shouldReceive('setValue')
            ->with($issueFeePerPermit)
            ->once();

        $guidanceTranslateableTextParameterIndex3 = m::mock(TranslateableTextParameter::class);
        $guidanceTranslateableTextParameterIndex3->shouldReceive('setValue')
            ->with($applicationFeePerPermit)
            ->once();

        $guidanceTranslateableText = m::mock(TranslateableText::class);
        $guidanceTranslateableText->shouldReceive('getParameter')
            ->with(0)
            ->andReturn($guidanceTranslateableTextParameterIndex0);
        $guidanceTranslateableText->shouldReceive('getParameter')
            ->with(1)
            ->andReturn($guidanceTranslateableTextParameterIndex1);
        $guidanceTranslateableText->shouldReceive('getParameter')
            ->with(2)
            ->andReturn($guidanceTranslateableTextParameterIndex2);
        $guidanceTranslateableText->shouldReceive('getParameter')
            ->with(3)
            ->andReturn($guidanceTranslateableTextParameterIndex3);

        $questionText = m::mock(QuestionText::class);
        $questionText->shouldReceive('getGuidance->getTranslateableText')
            ->andReturn($guidanceTranslateableText);

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

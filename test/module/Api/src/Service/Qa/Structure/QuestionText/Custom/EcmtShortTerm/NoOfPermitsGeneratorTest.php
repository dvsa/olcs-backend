<?php

namespace Dvsa\OlcsTest\Api\Service\Qa\Structure\QuestionText\Custom\EcmtShortTerm;

use Dvsa\Olcs\Api\Service\Qa\Structure\QuestionText\Custom\EcmtShortTerm\NoOfPermitsGenerator;
use Dvsa\Olcs\Api\Service\Qa\Structure\QuestionText\QuestionTextGenerator;
use Dvsa\Olcs\Api\Service\Qa\Structure\QuestionText\QuestionTextGeneratorContext;
use Dvsa\Olcs\Api\Domain\Repository\FeeType as FeeTypeRepository;
use Dvsa\Olcs\Api\Entity\Fee\FeeType as FeeTypeEntity;
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
        $feePerPermit = '20.00';

        $applicationFeeTypeEntity = m::mock(FeeTypeEntity::class);
        $issueFeeTypeEntity = m::mock(FeeTypeEntity::class);

        $translateableTextParameter = m::mock(TranslateableTextParameter::class);
        $translateableTextParameter->shouldReceive('setValue')
            ->with($feePerPermit)
            ->once();

        $questionText = m::mock(QuestionText::class);
        $questionText->shouldReceive('getGuidance->getTranslateableText->getParameter')
            ->with(0)
            ->andReturn($translateableTextParameter);

        $questionTextGeneratorContext = m::mock(QuestionTextGeneratorContext::class);
        $questionTextGeneratorContext->shouldReceive('getIrhpApplicationEntity->getFeePerPermit')
            ->with($applicationFeeTypeEntity, $issueFeeTypeEntity)
            ->andReturn($feePerPermit);

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

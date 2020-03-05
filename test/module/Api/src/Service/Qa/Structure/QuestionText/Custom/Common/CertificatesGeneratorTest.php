<?php

namespace Dvsa\OlcsTest\Api\Service\Qa\Structure\QuestionText\Custom\Common;

use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication as IrhpApplicationEntity;
use Dvsa\Olcs\Api\Service\Qa\QaContext;
use Dvsa\Olcs\Api\Service\Qa\Structure\QuestionText\Custom\Common\CertificatesGenerator;
use Dvsa\Olcs\Api\Service\Qa\Structure\QuestionText\QuestionTextGenerator;
use Dvsa\Olcs\Api\Service\Qa\Structure\QuestionText\QuestionText;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * CertificatesGeneratorTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class CertificatesGeneratorTest extends MockeryTestCase
{
    private $irhpApplicationEntity;

    private $questionText;

    private $qaContext;

    private $questionTextGenerator;

    public function setUp()
    {
        $this->irhpApplicationEntity = m::mock(IrhpApplicationEntity::class);

        $this->questionText = m::mock(QuestionText::class);

        $this->qaContext = m::mock(QaContext::class);
        $this->qaContext->shouldReceive('getQaEntity')
            ->andReturn($this->irhpApplicationEntity);

        $this->questionTextGenerator = m::mock(QuestionTextGenerator::class);
        $this->questionTextGenerator->shouldReceive('generate')
            ->with($this->qaContext)
            ->andReturn($this->questionText);
    }

    public function testGenerateUpdateKey()
    {
        $this->irhpApplicationEntity->shouldReceive('getIrhpPermitType->isEcmtRemoval')
            ->withNoArgs()
            ->andReturn(true);

        $additionalGuidanceTranslateableText = m::mock(TranslateableText::class);
        $additionalGuidanceTranslateableText->shouldReceive('setKey')
            ->with('qanda.common.certificates.additional-guidance.ecmt-removal')
            ->once();

        $this->questionText->shouldReceive('getAdditionalGuidance->getTranslateableText')
            ->andReturn($additionalGuidanceTranslateableText);

        $certificatesGenerator = new CertificatesGenerator($this->questionTextGenerator);

        $this->assertSame(
            $this->questionText,
            $certificatesGenerator->generate($this->qaContext)
        );
    }

    public function testGenerateNoKeyUpdate()
    {
        $this->irhpApplicationEntity->shouldReceive('getIrhpPermitType->isEcmtRemoval')
            ->withNoArgs()
            ->andReturn(false);

        $certificatesGenerator = new CertificatesGenerator($this->questionTextGenerator);

        $this->assertSame(
            $this->questionText,
            $certificatesGenerator->generate($this->qaContext)
        );
    }
}

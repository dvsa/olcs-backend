<?php

namespace Dvsa\OlcsTest\Api\Service\Permits\Bilateral\Internal;

use Dvsa\Olcs\Api\Entity\Generic\ApplicationStep;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication;
use Dvsa\Olcs\Api\Service\Permits\Bilateral\Internal\GenericAnswerUpdater;
use Dvsa\Olcs\Api\Service\Qa\AnswerSaver\GenericAnswerWriter;
use Dvsa\Olcs\Api\Service\Qa\QaContext;
use Dvsa\Olcs\Api\Service\Qa\QaContextFactory;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * GenericAnswerUpdaterTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class GenericAnswerUpdaterTest extends MockeryTestCase
{
    public function testUpdate()
    {
        $questionId = 77;
        $answerValue = 'answer_value';

        $applicationStep = m::mock(ApplicationStep::class);

        $irhpPermitApplication = m::mock(IrhpPermitApplication::class);
        $irhpPermitApplication->shouldReceive('getActiveApplicationPath->getApplicationStepByQuestionId')
            ->with($questionId)
            ->andReturn($applicationStep);

        $qaContext = m::mock(QaContext::class);

        $qaContextFactory = m::mock(QaContextFactory::class);
        $qaContextFactory->shouldReceive('create')
            ->with($applicationStep, $irhpPermitApplication)
            ->andReturn($qaContext);

        $genericAnswerWriter = m::mock(GenericAnswerWriter::class);
        $genericAnswerWriter->shouldReceive('write')
            ->with($qaContext, $answerValue);

        $permitUsageAnswerUpdater = new GenericAnswerUpdater($qaContextFactory, $genericAnswerWriter);
        $permitUsageAnswerUpdater->update($irhpPermitApplication, $questionId, $answerValue);
    }
}

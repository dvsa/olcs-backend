<?php

namespace Dvsa\OlcsTest\Api\Service\Permits\Bilateral\Internal;

use Dvsa\Olcs\Api\Entity\Generic\ApplicationStep;
use Dvsa\Olcs\Api\Entity\Generic\Question;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication;
use Dvsa\Olcs\Api\Service\Permits\Bilateral\Internal\PermitUsageAnswerUpdater;
use Dvsa\Olcs\Api\Service\Qa\AnswerSaver\GenericAnswerWriter;
use Dvsa\Olcs\Api\Service\Qa\QaContext;
use Dvsa\Olcs\Api\Service\Qa\QaContextFactory;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * PermitUsageAnswerUpdaterTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class PermitUsageAnswerUpdaterTest extends MockeryTestCase
{
    public function testUpdate()
    {
        $applicationStep = m::mock(ApplicationStep::class);

        $irhpPermitApplication = m::mock(IrhpPermitApplication::class);
        $irhpPermitApplication->shouldReceive('getActiveApplicationPath->getApplicationStepByQuestionId')
            ->with(Question::QUESTION_ID_BILATERAL_PERMIT_USAGE)
            ->andReturn($applicationStep);

        $permitUsageSelection = RefData::JOURNEY_SINGLE;

        $qaContext = m::mock(QaContext::class);

        $qaContextFactory = m::mock(QaContextFactory::class);
        $qaContextFactory->shouldReceive('create')
            ->with($applicationStep, $irhpPermitApplication)
            ->andReturn($qaContext);

        $genericAnswerWriter = m::mock(GenericAnswerWriter::class);
        $genericAnswerWriter->shouldReceive('write')
            ->with($qaContext, $permitUsageSelection);

        $permitUsageAnswerUpdater = new PermitUsageAnswerUpdater($qaContextFactory, $genericAnswerWriter);
        $permitUsageAnswerUpdater->update($irhpPermitApplication, $permitUsageSelection);
    }
}

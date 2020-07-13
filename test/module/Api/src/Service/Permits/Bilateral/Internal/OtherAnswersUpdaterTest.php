<?php

namespace Dvsa\OlcsTest\Api\Service\Permits\Bilateral\Internal;

use Dvsa\Olcs\Api\Entity\Generic\Question;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Api\Service\Permits\Bilateral\Internal\ApplicationPathAnswersUpdaterInterface;
use Dvsa\Olcs\Api\Service\Permits\Bilateral\Internal\ApplicationPathAnswersUpdaterProvider;
use Dvsa\Olcs\Api\Service\Permits\Bilateral\Internal\GenericAnswerUpdater;
use Dvsa\Olcs\Api\Service\Permits\Bilateral\Internal\OtherAnswersUpdater;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * OtherAnswersUpdaterTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class OtherAnswersUpdaterTest extends MockeryTestCase
{
    public function testUpdate()
    {
        $applicationPathGroupId = 67;

        $irhpPermitApplication = m::mock(IrhpPermitApplication::class);
        $irhpPermitApplication->shouldReceive('getActiveApplicationPath->getApplicationPathGroup->getId')
            ->withNoArgs()
            ->andReturn($applicationPathGroupId);
        $irhpPermitApplication->shouldReceive('updateCheckAnswers')
            ->withNoArgs()
            ->once();

        $bilateralRequired = [
            IrhpPermitApplication::BILATERAL_STANDARD_REQUIRED => 7,
            IrhpPermitApplication::BILATERAL_CABOTAGE_REQUIRED => 10
        ];

        $permitUsageSelection = RefData::JOURNEY_SINGLE;

        $genericAnswerUpdater = m::mock(GenericAnswerUpdater::class);
        $genericAnswerUpdater->shouldReceive('update')
            ->with($irhpPermitApplication, Question::QUESTION_ID_BILATERAL_PERMIT_USAGE, $permitUsageSelection)
            ->once();

        $applicationPathAnswersUpdater = m::mock(ApplicationPathAnswersUpdaterInterface::class);
        $applicationPathAnswersUpdater->shouldReceive('update')
            ->with($irhpPermitApplication, $bilateralRequired)
            ->once();

        $applicationPathAnswersUpdaterProvider = m::mock(ApplicationPathAnswersUpdaterProvider::class);
        $applicationPathAnswersUpdaterProvider->shouldReceive('getByApplicationPathGroupId')
            ->with($applicationPathGroupId)
            ->andReturn($applicationPathAnswersUpdater);

        $otherAnswersUpdater = new OtherAnswersUpdater($genericAnswerUpdater, $applicationPathAnswersUpdaterProvider);
        $otherAnswersUpdater->update($irhpPermitApplication, $bilateralRequired, $permitUsageSelection);
    }
}

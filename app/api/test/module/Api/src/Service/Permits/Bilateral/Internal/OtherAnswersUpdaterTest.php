<?php

namespace Dvsa\OlcsTest\Api\Service\Permits\Bilateral\Internal;

use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Api\Service\Permits\Bilateral\Internal\CabotageAnswerUpdater;
use Dvsa\Olcs\Api\Service\Permits\Bilateral\Internal\OtherAnswersUpdater;
use Dvsa\Olcs\Api\Service\Permits\Bilateral\Internal\PermitUsageAnswerUpdater;
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
        $irhpPermitApplication = m::mock(IrhpPermitApplication::class);

        $irhpPermitApplication->shouldReceive('updateCheckAnswers')
            ->withNoArgs()
            ->once();

        $bilateralRequired = [
            IrhpPermitApplication::BILATERAL_STANDARD_REQUIRED => 7,
            IrhpPermitApplication::BILATERAL_CABOTAGE_REQUIRED => 10
        ];

        $permitUsageSelection = RefData::JOURNEY_SINGLE;

        $permitUsageAnswerUpdater = m::mock(PermitUsageAnswerUpdater::class);
        $permitUsageAnswerUpdater->shouldReceive('update')
            ->with($irhpPermitApplication, $permitUsageSelection)
            ->once();

        $cabotageAnswerUpdater = m::mock(CabotageAnswerUpdater::class);
        $cabotageAnswerUpdater->shouldReceive('update')
            ->with($irhpPermitApplication, $bilateralRequired)
            ->once();

        $otherAnswersUpdater = new OtherAnswersUpdater($permitUsageAnswerUpdater, $cabotageAnswerUpdater);
        $otherAnswersUpdater->update($irhpPermitApplication, $bilateralRequired, $permitUsageSelection);
    }
}

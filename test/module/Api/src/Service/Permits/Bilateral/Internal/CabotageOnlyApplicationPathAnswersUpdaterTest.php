<?php

namespace Dvsa\OlcsTest\Api\Service\Permits\Bilateral\Internal;

use Dvsa\Olcs\Api\Entity\Generic\Answer;
use Dvsa\Olcs\Api\Entity\Generic\Question;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication;
use Dvsa\Olcs\Api\Service\Permits\Bilateral\Internal\GenericAnswerUpdater;
use Dvsa\Olcs\Api\Service\Permits\Bilateral\Internal\CabotageOnlyApplicationPathAnswersUpdater;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * CabotageOnlyApplicationPathAnswersUpdaterTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class CabotageOnlyApplicationPathAnswersUpdaterTest extends MockeryTestCase
{
    public function testUpdate()
    {
        $irhpPermitApplication = m::mock(IrhpPermitApplication::class);

        $bilateralRequired = [
            IrhpPermitApplication::BILATERAL_STANDARD_REQUIRED => 7,
            IrhpPermitApplication::BILATERAL_CABOTAGE_REQUIRED => 10
        ];

        $genericAnswerUpdater = m::mock(GenericAnswerUpdater::class);
        $genericAnswerUpdater->shouldReceive('update')
            ->with(
                $irhpPermitApplication,
                Question::QUESTION_ID_BILATERAL_CABOTAGE_ONLY,
                Answer::BILATERAL_CABOTAGE_ONLY
            )
            ->once();

        $cabotageOnlyApplicationPathAnswersUpdater = new CabotageOnlyApplicationPathAnswersUpdater($genericAnswerUpdater);
        $cabotageOnlyApplicationPathAnswersUpdater->update($irhpPermitApplication, $bilateralRequired);
    }
}

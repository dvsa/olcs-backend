<?php

namespace Dvsa\OlcsTest\Api\Service\Permits\Bilateral\Internal;

use Dvsa\Olcs\Api\Entity\Generic\Question;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication;
use Dvsa\Olcs\Api\Service\Permits\Bilateral\Internal\GenericAnswerUpdater;
use Dvsa\Olcs\Api\Service\Permits\Bilateral\Internal\TurkeyApplicationPathAnswersUpdater;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Bilateral\ThirdCountryAnswerSaver;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * TurkeyApplicationPathAnswersUpdaterTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class TurkeyApplicationPathAnswersUpdaterTest extends MockeryTestCase
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
                Question::QUESTION_ID_BILATERAL_THIRD_COUNTRY,
                ThirdCountryAnswerSaver::YES_ANSWER
            )
            ->once();

        $turkeyApplicationPathAnswersUpdater = new TurkeyApplicationPathAnswersUpdater($genericAnswerUpdater);
        $turkeyApplicationPathAnswersUpdater->update($irhpPermitApplication, $bilateralRequired);
    }
}

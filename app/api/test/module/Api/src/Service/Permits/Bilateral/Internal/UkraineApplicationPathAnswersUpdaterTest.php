<?php

namespace Dvsa\OlcsTest\Api\Service\Permits\Bilateral\Internal;

use Dvsa\Olcs\Api\Entity\Generic\Question;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication;
use Dvsa\Olcs\Api\Service\Permits\Bilateral\Internal\GenericAnswerUpdater;
use Dvsa\Olcs\Api\Service\Permits\Bilateral\Internal\UkraineApplicationPathAnswersUpdater;
use Dvsa\Olcs\Api\Service\Qa\Structure\Element\Custom\Bilateral\EmissionsStandardsAnswerSaver;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * UkraineApplicationPathAnswersUpdaterTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class UkraineApplicationPathAnswersUpdaterTest extends MockeryTestCase
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
                Question::QUESTION_ID_BILATERAL_EMISSIONS_STANDARDS,
                EmissionsStandardsAnswerSaver::EURO3_OR_EURO4_ANSWER
            )
            ->once();

        $ukraineApplicationPathAnswersUpdater = new UkraineApplicationPathAnswersUpdater($genericAnswerUpdater);
        $ukraineApplicationPathAnswersUpdater->update($irhpPermitApplication, $bilateralRequired);
    }
}

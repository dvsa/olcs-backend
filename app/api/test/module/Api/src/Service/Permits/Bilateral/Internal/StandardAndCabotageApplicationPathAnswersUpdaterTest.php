<?php

namespace Dvsa\OlcsTest\Api\Service\Permits\Bilateral\Internal;

use Dvsa\Olcs\Api\Entity\Generic\Answer;
use Dvsa\Olcs\Api\Entity\Generic\Question;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication;
use Dvsa\Olcs\Api\Service\Permits\Bilateral\Internal\GenericAnswerUpdater;
use Dvsa\Olcs\Api\Service\Permits\Bilateral\Internal\StandardAndCabotageApplicationPathAnswersUpdater;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * StandardAndCabotageApplicationPathAnswersUpdaterTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class StandardAndCabotageApplicationPathAnswersUpdaterTest extends MockeryTestCase
{
    /**
     * @dataProvider dpUpdate
     */
    public function testUpdate($bilateralRequired, $expectedAnswer)
    {
        $irhpPermitApplication = m::mock(IrhpPermitApplication::class);

        $genericAnswerUpdater = m::mock(GenericAnswerUpdater::class);
        $genericAnswerUpdater->shouldReceive('update')
            ->with(
                $irhpPermitApplication,
                Question::QUESTION_ID_BILATERAL_STANDARD_AND_CABOTAGE,
                $expectedAnswer
            )
            ->once();

        $standardAndCabotageApplicationPathAnswersUpdater = new StandardAndCabotageApplicationPathAnswersUpdater(
            $genericAnswerUpdater
        );

        $standardAndCabotageApplicationPathAnswersUpdater->update($irhpPermitApplication, $bilateralRequired);
    }

    public function dpUpdate()
    {
        return [
            'standard only' => [
                [
                    IrhpPermitApplication::BILATERAL_STANDARD_REQUIRED => 3,
                    IrhpPermitApplication::BILATERAL_CABOTAGE_REQUIRED => null
                ],
                Answer::BILATERAL_STANDARD_ONLY
            ],
            'cabotage only' => [
                [
                    IrhpPermitApplication::BILATERAL_STANDARD_REQUIRED => null,
                    IrhpPermitApplication::BILATERAL_CABOTAGE_REQUIRED => 7
                ],
                Answer::BILATERAL_CABOTAGE_ONLY
            ],
            'standard and cabotage' => [
                [
                    IrhpPermitApplication::BILATERAL_STANDARD_REQUIRED => 4,
                    IrhpPermitApplication::BILATERAL_CABOTAGE_REQUIRED => 7
                ],
                Answer::BILATERAL_STANDARD_AND_CABOTAGE
            ]
        ];
    }
}

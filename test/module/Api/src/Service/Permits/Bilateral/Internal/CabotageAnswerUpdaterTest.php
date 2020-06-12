<?php

namespace Dvsa\OlcsTest\Api\Service\Permits\Bilateral\Internal;

use Dvsa\Olcs\Api\Service\Permits\Bilateral\Internal\CabotageAnswerUpdater;
use Dvsa\Olcs\Api\Entity\Generic\Answer;
use Dvsa\Olcs\Api\Entity\Generic\ApplicationPath;
use Dvsa\Olcs\Api\Entity\Generic\ApplicationPathGroup;
use Dvsa\Olcs\Api\Entity\Generic\ApplicationStep;
use Dvsa\Olcs\Api\Entity\Generic\Question;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication;
use Dvsa\Olcs\Api\Service\Qa\AnswerSaver\GenericAnswerWriter;
use Dvsa\Olcs\Api\Service\Qa\QaContext;
use Dvsa\Olcs\Api\Service\Qa\QaContextFactory;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * CabotageAnswerUpdaterTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class CabotageAnswerUpdaterTest extends MockeryTestCase
{
    private $qaContext;

    private $applicationStep;

    private $applicationPath;

    private $applicationPathGroup;

    private $irhpPermitApplication;

    private $qaContextFactory;

    private $genericAnswerWriter;

    private $cabotageAnswerUpdater;

    public function setUp()
    {
        $this->qaContext = m::mock(QaContext::class);

        $this->applicationStep = m::mock(ApplicationStep::class);

        $this->applicationPathGroup = m::mock(ApplicationPathGroup::class);

        $this->applicationPath = m::mock(ApplicationPath::class);
        $this->applicationPath->shouldReceive('getApplicationPathGroup')
            ->withNoArgs()
            ->andReturn($this->applicationPathGroup);

        $this->irhpPermitApplication = m::mock(IrhpPermitApplication::class);
        $this->irhpPermitApplication->shouldReceive('getActiveApplicationPath')
            ->andReturn($this->applicationPath);

        $this->qaContextFactory = m::mock(QaContextFactory::class);

        $this->genericAnswerWriter = m::mock(GenericAnswerWriter::class);

        $this->cabotageAnswerUpdater = new CabotageAnswerUpdater($this->qaContextFactory, $this->genericAnswerWriter);
    }

    public function testUpdateCabotageOnly()
    {
        $this->applicationPathGroup->shouldReceive('isBilateralCabotageOnly')
            ->withNoArgs()
            ->andReturn(true);

        $this->applicationPath->shouldReceive('getApplicationStepByQuestionId')
            ->with(Question::QUESTION_ID_BILATERAL_CABOTAGE_ONLY)
            ->andReturn($this->applicationStep);

        $this->qaContextFactory->shouldReceive('create')
            ->with($this->applicationStep, $this->irhpPermitApplication)
            ->andReturn($this->qaContext);

        $this->genericAnswerWriter->shouldReceive('write')
            ->with($this->qaContext, Answer::BILATERAL_CABOTAGE_ONLY, Question::QUESTION_TYPE_STRING)
            ->once();

        $bilateralRequired = [
            IrhpPermitApplication::BILATERAL_STANDARD_REQUIRED => null,
            IrhpPermitApplication::BILATERAL_CABOTAGE_REQUIRED => 6
        ];

        $this->cabotageAnswerUpdater->update($this->irhpPermitApplication, $bilateralRequired);
    }

    /**
     * @dataProvider dpUpdateStandardAndCabotage
     */
    public function testUpdateStandardAndCabotage($bilateralRequired, $expectedAnswer)
    {
        $this->applicationPathGroup->shouldReceive('isBilateralCabotageOnly')
            ->withNoArgs()
            ->andReturn(false);

        $this->applicationPathGroup->shouldReceive('isBilateralStandardAndCabotage')
            ->withNoArgs()
            ->andReturn(true);

        $this->applicationPath->shouldReceive('getApplicationStepByQuestionId')
            ->with(Question::QUESTION_ID_BILATERAL_STANDARD_AND_CABOTAGE)
            ->andReturn($this->applicationStep);

        $this->qaContextFactory->shouldReceive('create')
            ->with($this->applicationStep, $this->irhpPermitApplication)
            ->andReturn($this->qaContext);

        $this->genericAnswerWriter->shouldReceive('write')
            ->with($this->qaContext, $expectedAnswer, Question::QUESTION_TYPE_STRING)
            ->once();

        $this->cabotageAnswerUpdater->update($this->irhpPermitApplication, $bilateralRequired);
    }

    public function dpUpdateStandardAndCabotage()
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

    public function testUpdateStandardOnly()
    {
        $this->applicationPathGroup->shouldReceive('isBilateralCabotageOnly')
            ->withNoArgs()
            ->andReturn(false);

        $this->applicationPathGroup->shouldReceive('isBilateralStandardAndCabotage')
            ->withNoArgs()
            ->andReturn(false);

        $bilateralRequired = [
            IrhpPermitApplication::BILATERAL_STANDARD_REQUIRED => 9,
            IrhpPermitApplication::BILATERAL_CABOTAGE_REQUIRED => null
        ];

        $this->cabotageAnswerUpdater->update($this->irhpPermitApplication, $bilateralRequired);
    }
}

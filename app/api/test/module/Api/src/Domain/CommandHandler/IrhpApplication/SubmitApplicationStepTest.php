<?php

/**
 * Submit Application Step test
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\IrhpApplication;

use Dvsa\Olcs\Api\Domain\CommandHandler\IrhpApplication\SubmitApplicationStep as Sut;
use Dvsa\Olcs\Api\Entity\Generic\ApplicationStep as ApplicationStepEntity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication as IrhpApplicationEntity;
use Dvsa\Olcs\Api\Service\Qa\ApplicationStepObjectsProvider;
use Dvsa\Olcs\Api\Service\Qa\FormControlStrategyProvider;
use Dvsa\Olcs\Api\Service\Qa\Strategy\FormControlStrategyInterface;
use Dvsa\Olcs\Transfer\Command\IrhpApplication\SubmitApplicationStep as Cmd;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;

/**
 * Submit Application Step test
 */
class SubmitApplicationStepTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new Sut();

        $this->mockedSmServices = [
            'QaApplicationStepObjectsProvider' => m::mock(ApplicationStepObjectsProvider::class),
            'QaFormControlStrategyProvider' => m::mock(FormControlStrategyProvider::class)
        ];

        parent::setUp();
    }

    public function testHandleCommand()
    {
        $irhpApplicationId = 457;
        $slug = 'removals-eligibility';

        $applicationStepEntity = m::mock(ApplicationStepEntity::class);
        $irhpApplicationEntity = m::mock(IrhpApplicationEntity::class);

        $applicationStepObjects = [
            'applicationStep' => $applicationStepEntity,
            'irhpApplication' => $irhpApplicationEntity
        ];

        $this->mockedSmServices['QaApplicationStepObjectsProvider']->shouldReceive('getObjects')
            ->with($irhpApplicationId, $slug)
            ->andReturn($applicationStepObjects);

        $postData = [
            'fieldset123' => [
                'qaElement' => '123'
            ]
        ];

        $formControlStrategy = m::mock(FormControlStrategyInterface::class);
        $formControlStrategy->shouldReceive('saveFormData')
            ->with($applicationStepEntity, $irhpApplicationEntity, $postData)
            ->once();

        $this->mockedSmServices['QaFormControlStrategyProvider']->shouldReceive('get')
            ->with($applicationStepEntity)
            ->andReturn($formControlStrategy);

        $command = Cmd::create(
            [
                'id' => $irhpApplicationId,
                'slug' => $slug,
                'postData' => $postData
            ]
        );

        $this->sut->handleCommand($command);
    }
}

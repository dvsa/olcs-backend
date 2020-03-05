<?php

/**
 * Submit Application Step test
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\IrhpApplication;

use Dvsa\Olcs\Api\Domain\CommandHandler\IrhpApplication\SubmitApplicationStep as Sut;
use Dvsa\Olcs\Api\Domain\Repository\IrhpApplication as IrhpApplicationRepo;
use Dvsa\Olcs\Api\Entity\Generic\ApplicationStep as ApplicationStepEntity;
use Dvsa\Olcs\Api\Service\Qa\QaContextGenerator;
use Dvsa\Olcs\Api\Service\Qa\QaContext;
use Dvsa\Olcs\Api\Service\Qa\QaEntityInterface;
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

        $this->mockRepo('IrhpApplication', IrhpApplicationRepo::class);

        $this->mockedSmServices = [
            'QaContextGenerator' => m::mock(QaContextGenerator::class),
            'QaFormControlStrategyProvider' => m::mock(FormControlStrategyProvider::class)
        ];

        parent::setUp();
    }

    public function testHandleCommand()
    {
        $irhpApplicationId = 23;
        $irhpPermitApplicationId = 457;
        $slug = 'removals-eligibility';

        $applicationStepEntity = m::mock(ApplicationStepEntity::class);
        $qaEntity = m::mock(QaEntityInterface::class);
        $qaEntity->shouldReceive('onSubmitApplicationStep')
            ->withNoArgs()
            ->once();

        $qaContext = m::mock(QaContext::class);
        $qaContext->shouldReceive('getApplicationStepEntity')
            ->withNoArgs()
            ->andReturn($applicationStepEntity);
        $qaContext->shouldReceive('getQaEntity')
            ->withNoArgs()
            ->andReturn($qaEntity);

        $this->mockedSmServices['QaContextGenerator']->shouldReceive('generate')
            ->with($irhpApplicationId, $irhpPermitApplicationId, $slug)
            ->andReturn($qaContext);

        $postData = [
            'fieldset123' => [
                'qaElement' => '123'
            ]
        ];

        $formControlStrategy = m::mock(FormControlStrategyInterface::class);
        $formControlStrategy->shouldReceive('saveFormData')
            ->with($qaContext, $postData)
            ->once();

        $this->repoMap['IrhpApplication']->shouldReceive('save')
            ->with($qaEntity)
            ->once();

        $this->mockedSmServices['QaFormControlStrategyProvider']->shouldReceive('get')
            ->with($applicationStepEntity)
            ->andReturn($formControlStrategy);

        $command = Cmd::create(
            [
                'id' => $irhpApplicationId,
                'irhpPermitApplication' => $irhpPermitApplicationId,
                'slug' => $slug,
                'postData' => $postData
            ]
        );

        $this->sut->handleCommand($command);
    }
}

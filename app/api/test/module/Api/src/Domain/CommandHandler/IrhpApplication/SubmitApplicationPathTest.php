<?php

/**
 * Submit Application Path test
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\IrhpApplication;

use DateTime;
use Dvsa\Olcs\Api\Domain\CommandHandler\IrhpApplication\SubmitApplicationPath as Sut;
use Dvsa\Olcs\Api\Domain\Repository\ApplicationPath as ApplicationPathRepo;
use Dvsa\Olcs\Api\Domain\Repository\IrhpApplication as IrhpApplicationRepo;
use Dvsa\Olcs\Api\Entity\Generic\ApplicationStep as ApplicationStepEntity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpApplication as IrhpApplicationEntity;
use Dvsa\Olcs\Api\Service\Qa\FormControlStrategyProvider;
use Dvsa\Olcs\Api\Service\Qa\Strategy\FormControlStrategyInterface;
use Dvsa\Olcs\Transfer\Command\IrhpApplication\SubmitApplicationPath as Cmd;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;

/**
 * Submit Application Path test
 */
class SubmitApplicationPathTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new Sut();

        $this->mockRepo('IrhpApplication', IrhpApplicationRepo::class);
        $this->mockRepo('ApplicationPath', ApplicationPathRepo::class);

        $this->mockedSmServices = [
            'QaFormControlStrategyProvider' => m::mock(FormControlStrategyProvider::class)
        ];

        parent::setUp();
    }

    public function testHandleCommand()
    {
        $irhpApplicationId = 459;
        $irhpApplicationCreatedOn = m::mock(DateTime::class);
        $irhpApplicationPermitTypeId = 52;

        $postData = [
            'fieldset123' => [
                'qaElement' => '123'
            ]
        ];

        $command = Cmd::create(
            [
                'id' => $irhpApplicationId,
                'postData' => $postData
            ]
        );

        $irhpApplicationEntity = m::mock(IrhpApplicationEntity::class);
        $irhpApplicationEntity->shouldReceive('getIrhpPermitType->getId')
            ->andReturn($irhpApplicationPermitTypeId);
        $irhpApplicationEntity->shouldReceive('getApplicationPathLockedOn')
            ->withNoArgs()
            ->andReturn($irhpApplicationCreatedOn);

        $this->repoMap['IrhpApplication']->shouldReceive('fetchUsingId')
            ->with($command)
            ->andReturn($irhpApplicationEntity);

        $applicationStepEntity1 = m::mock(ApplicationStepEntity::class);
        $applicationStepEntity2 = m::mock(ApplicationStepEntity::class);

        $applicationStepEntity1Strategy = m::mock(FormControlStrategyInterface::class);
        $applicationStepEntity1Strategy->shouldReceive('saveFormData')
            ->with($applicationStepEntity1, $irhpApplicationEntity, $postData)
            ->once();

        $applicationStepEntity2Strategy = m::mock(FormControlStrategyInterface::class);
        $applicationStepEntity2Strategy->shouldReceive('saveFormData')
            ->with($applicationStepEntity2, $irhpApplicationEntity, $postData)
            ->once();

        $this->mockedSmServices['QaFormControlStrategyProvider']->shouldReceive('get')
            ->with($applicationStepEntity1)
            ->andReturn($applicationStepEntity1Strategy);
        $this->mockedSmServices['QaFormControlStrategyProvider']->shouldReceive('get')
            ->with($applicationStepEntity2)
            ->andReturn($applicationStepEntity2Strategy);

        $applicationStepEntities = [
            $applicationStepEntity1,
            $applicationStepEntity2,
        ];
 
        $applicationPathEntity = m::mock(ApplicationPathEntity::class);
        $applicationPathEntity->shouldReceive('getApplicationSteps')
            ->andReturn($applicationStepEntities);

        $this->repoMap['ApplicationPath']->shouldReceive('fetchByIrhpPermitTypeIdAndDate')
            ->with($irhpApplicationPermitTypeId, $irhpApplicationCreatedOn)
            ->andReturn($applicationPathEntity);

        $this->sut->handleCommand($command);
    }
}

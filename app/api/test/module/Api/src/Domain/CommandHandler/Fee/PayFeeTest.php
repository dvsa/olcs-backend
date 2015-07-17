<?php

/**
 * Pay Fee Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Fee;

use Dvsa\Olcs\Api\Domain\Command\Application\Grant\ValidateApplication;
use Dvsa\Olcs\Api\Domain\Command\Fee\PayFee as PayFeeCommand;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\Fee\PayFee;
use Dvsa\Olcs\Api\Domain\Repository\Fee as FeeRepo;
use Dvsa\Olcs\Api\Entity\Application\Application;
use Dvsa\Olcs\Api\Entity\Fee\Fee as FeeEntity;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;

/**
 * Pay Fee Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class PayFeeTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new PayFee();
        $this->mockRepo('Fee', FeeRepo::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            Application::APPLICATION_STATUS_UNDER_CONSIDERATION,
            Application::APPLICATION_STATUS_GRANTED
        ];

        parent::initReferences();
    }

    public function testHandleCommandWithoutApplication()
    {
        $command = PayFeeCommand::create(['id' => 111]);

        /** @var FeeEntity $fee */
        $fee = m::mock(FeeEntity::class)->makePartial();

        $this->repoMap['Fee']->shouldReceive('fetchUsingId')
            ->with($command)
            ->andReturn($fee);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => []
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    public function testHandleCommandWithVariation()
    {
        $command = PayFeeCommand::create(['id' => 111]);

        /** @var Application $application */
        $application = m::mock(Application::class)->makePartial();
        $application->setIsVariation(true);

        /** @var FeeEntity $fee */
        $fee = m::mock(FeeEntity::class)->makePartial();
        $fee->setApplication($application);

        $this->repoMap['Fee']->shouldReceive('fetchUsingId')
            ->with($command)
            ->andReturn($fee);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => []
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    public function testHandleCommandWithUnGrantedApplication()
    {
        $command = PayFeeCommand::create(['id' => 111]);

        /** @var Application $application */
        $application = m::mock(Application::class)->makePartial();
        $application->setIsVariation(false);
        $application->setStatus($this->refData[Application::APPLICATION_STATUS_UNDER_CONSIDERATION]);

        /** @var FeeEntity $fee */
        $fee = m::mock(FeeEntity::class)->makePartial();
        $fee->setApplication($application);

        $this->repoMap['Fee']->shouldReceive('fetchUsingId')
            ->with($command)
            ->andReturn($fee);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => []
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    public function testHandleCommandWithOutstandingApplicationFees()
    {
        $command = PayFeeCommand::create(['id' => 111]);

        /** @var Application $application */
        $application = m::mock(Application::class)->makePartial();
        $application->setId(222);
        $application->setIsVariation(false);
        $application->setStatus($this->refData[Application::APPLICATION_STATUS_GRANTED]);

        /** @var FeeEntity $fee */
        $fee = m::mock(FeeEntity::class)->makePartial();
        $fee->setApplication($application);

        $this->repoMap['Fee']->shouldReceive('fetchUsingId')
            ->with($command)
            ->andReturn($fee)
            ->shouldReceive('fetchOutstandingGrantFeesByApplicationId')
            ->once()
            ->with(222)
            ->andReturn(['foo']);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => []
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    public function testHandleCommandWithoutOutstandingApplicationFees()
    {
        $command = PayFeeCommand::create(['id' => 111]);

        /** @var Application $application */
        $application = m::mock(Application::class)->makePartial();
        $application->setId(222);
        $application->setIsVariation(false);
        $application->setStatus($this->refData[Application::APPLICATION_STATUS_GRANTED]);

        /** @var FeeEntity $fee */
        $fee = m::mock(FeeEntity::class)->makePartial();
        $fee->setApplication($application);

        $this->repoMap['Fee']->shouldReceive('fetchUsingId')
            ->with($command)
            ->andReturn($fee)
            ->shouldReceive('fetchOutstandingGrantFeesByApplicationId')
            ->once()
            ->with(222)
            ->andReturn([]);

        $result1 = new Result();
        $result1->addMessage('ValidateApplication');
        $this->expectedSideEffect(ValidateApplication::class, ['id' => 222], $result1);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                'ValidateApplication'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }
}

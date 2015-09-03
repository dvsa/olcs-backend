<?php

/**
 * Pay Fee Test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Fee;

use Dvsa\Olcs\Api\Domain\Command\Application\Grant\ValidateApplication;
use Dvsa\Olcs\Api\Domain\Command\Application\InForceInterim;
use Dvsa\Olcs\Api\Domain\Command\Fee\PayFee as PayFeeCommand;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\Fee\PayFee;
use Dvsa\Olcs\Api\Domain\Repository\Fee as FeeRepo;
use Dvsa\Olcs\Api\Entity\Application\Application;
use Dvsa\Olcs\Api\Entity\Fee\Fee as FeeEntity;
use Dvsa\Olcs\Api\Entity\Fee\FeeType;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\Command\Application\EndInterim as EndInterimCmd;

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
        $this->mockRepo('ContinuationDetail', \Dvsa\Olcs\Api\Domain\Repository\ContinuationDetail::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            Application::APPLICATION_STATUS_UNDER_CONSIDERATION,
            Application::APPLICATION_STATUS_GRANTED,
            Application::INTERIM_STATUS_GRANTED,
            Application::INTERIM_STATUS_INFORCE,
        ];

        parent::initReferences();
    }

    public function testHandleCommandWithoutApplication()
    {
        $command = PayFeeCommand::create(['id' => 111]);

        /** @var FeeEntity $fee */
        $fee = m::mock(FeeEntity::class)->makePartial();
        $fee->shouldReceive('getFeeType->getFeeType->getId')->andReturn('foo');

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
        $fee->shouldReceive('getFeeType->getFeeType->getId')->andReturn('foo');

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
        $fee->shouldReceive('getFeeType->getFeeType->getId')->andReturn('foo');

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
        $fee->shouldReceive('getFeeType->getFeeType->getId')->andReturn('foo');

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
        $fee->shouldReceive('getFeeType->getFeeType->getId')->andReturn('foo');

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

    public function testHandleCommandContinuationNoContinuationDetail()
    {
        $command = PayFeeCommand::create(['id' => 111]);

        /** @var FeeEntity $fee */
        $fee = m::mock(FeeEntity::class)->makePartial();
        $fee->shouldReceive('getFeeType->getFeeType->getId')->andReturn('CONT');
        $fee->shouldReceive('getLicence->getId')->andReturn(717);

        $this->repoMap['Fee']->shouldReceive('fetchUsingId')->with($command)->andReturn($fee);
        $this->repoMap['ContinuationDetail']->shouldReceive('fetchOngoingForLicence')->with(717)
            ->andThrow(\Doctrine\ORM\UnexpectedResultException::class);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => []
        ];
        $this->assertEquals($expected, $result->toArray());
    }

    public function licenceStatusNotAllowed()
    {
        return [
            [\Dvsa\Olcs\Api\Entity\Licence\Licence::LICENCE_STATUS_CONTINUATION_NOT_SOUGHT],
            [\Dvsa\Olcs\Api\Entity\Licence\Licence::LICENCE_STATUS_GRANTED],
            [\Dvsa\Olcs\Api\Entity\Licence\Licence::LICENCE_STATUS_NOT_SUBMITTED],
            [\Dvsa\Olcs\Api\Entity\Licence\Licence::LICENCE_STATUS_NOT_TAKEN_UP],
            [\Dvsa\Olcs\Api\Entity\Licence\Licence::LICENCE_STATUS_REFUSED],
            [\Dvsa\Olcs\Api\Entity\Licence\Licence::LICENCE_STATUS_REVOKED],
            [\Dvsa\Olcs\Api\Entity\Licence\Licence::LICENCE_STATUS_SURRENDERED],
            [\Dvsa\Olcs\Api\Entity\Licence\Licence::LICENCE_STATUS_TERMINATED],
            [\Dvsa\Olcs\Api\Entity\Licence\Licence::LICENCE_STATUS_UNDER_CONSIDERATION],
            [\Dvsa\Olcs\Api\Entity\Licence\Licence::LICENCE_STATUS_WITHDRAWN],
        ];
    }

    /**
     * @dataProvider licenceStatusNotAllowed
     */
    public function testHandleCommandContinuationNotAllowedLicenceStatus($status)
    {
        $command = PayFeeCommand::create(['id' => 111]);

        /** @var FeeEntity $fee */
        $fee = m::mock(FeeEntity::class)->makePartial();
        $fee->shouldReceive('getFeeType->getFeeType->getId')->andReturn('CONT');
        $fee->shouldReceive('getLicence->getId')->andReturn(717);
        $fee->shouldReceive('getLicence->getStatus->getId')->andReturn($status);

        $this->repoMap['Fee']->shouldReceive('fetchUsingId')->with($command)->andReturn($fee);
        $this->repoMap['ContinuationDetail']->shouldReceive('fetchOngoingForLicence')->with(717)
            ->andReturn('FOO');

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => []
        ];
        $this->assertEquals($expected, $result->toArray());
    }

    public function testHandleCommandWithoutOutstandingApplicationFeesWithGrantIntWithoutInterimStatus()
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
        $fee->shouldReceive('getFeeType->getFeeType->getId')
            ->andReturn(FeeType::FEE_TYPE_GRANTINT);

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

    public function licenceStatusAllowed()
    {
        return [
            [\Dvsa\Olcs\Api\Entity\Licence\Licence::LICENCE_STATUS_CURTAILED],
            [\Dvsa\Olcs\Api\Entity\Licence\Licence::LICENCE_STATUS_SUSPENDED],
            [\Dvsa\Olcs\Api\Entity\Licence\Licence::LICENCE_STATUS_VALID],
        ];
    }

    /**
     * @dataProvider licenceStatusAllowed
     */
    public function testHandleCommandContinuationHasOutstandingFees($status)
    {
        $command = PayFeeCommand::create(['id' => 111]);

        /** @var FeeEntity $fee */
        $fee = m::mock(FeeEntity::class)->makePartial();
        $fee->shouldReceive('getFeeType->getFeeType->getId')->andReturn('CONT');
        $fee->shouldReceive('getLicence->getId')->andReturn(717);
        $fee->shouldReceive('getLicence->getStatus->getId')->andReturn($status);

        $this->repoMap['Fee']->shouldReceive('fetchUsingId')->with($command)->andReturn($fee);
        $this->repoMap['ContinuationDetail']->shouldReceive('fetchOngoingForLicence')->with(717)
            ->andReturn('FOO');

        $this->repoMap['Fee']->shouldReceive('fetchOutstandingContinuationFeesByLicenceId')->with(717)
            ->andReturn(['SOME']);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => []
        ];
        $this->assertEquals($expected, $result->toArray());
    }

    /**
     * @dataProvider licenceStatusAllowed
     */
    public function testHandleCommandContinuation($status)
    {
        $command = PayFeeCommand::create(['id' => 111]);

        /** @var FeeEntity $fee */
        $fee = m::mock(FeeEntity::class)->makePartial();
        $fee->shouldReceive('getFeeType->getFeeType->getId')->andReturn('CONT');
        $fee->shouldReceive('getLicence->getId')->andReturn(717);
        $fee->shouldReceive('getLicence->getStatus->getId')->andReturn($status);

        $this->repoMap['Fee']->shouldReceive('fetchUsingId')->with($command)->andReturn($fee);
        $this->repoMap['ContinuationDetail']->shouldReceive('fetchOngoingForLicence')->with(717)
            ->andReturn('FOO');

        $this->repoMap['Fee']->shouldReceive('fetchOutstandingContinuationFeesByLicenceId')->with(717)
            ->andReturn([]);

        $this->expectedSideEffect(
            \Dvsa\Olcs\Transfer\Command\Licence\ContinueLicence::class,
            ['id' => 717, 'version' => null],
            (new Result())->addMessage('XXX')
        );

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => ['XXX', '@todo Display message "licence.continued.message" to user']
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    public function testHandleCommandWithoutOutstandingApplicationFeesWithGrantInt()
    {
        $command = PayFeeCommand::create(['id' => 111]);

        /** @var Application $application */
        $application = m::mock(Application::class)->makePartial();
        $application->setId(222);
        $application->setIsVariation(false);
        $application->setStatus($this->refData[Application::APPLICATION_STATUS_GRANTED]);
        $application->setInterimStatus($this->refData[Application::INTERIM_STATUS_GRANTED]);

        /** @var FeeEntity $fee */
        $fee = m::mock(FeeEntity::class)->makePartial();
        $fee->setApplication($application);
        $fee->shouldReceive('getFeeType->getFeeType->getId')
            ->andReturn(FeeType::FEE_TYPE_GRANTINT);

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

        $result2 = new Result();
        $result2->addMessage('InForceInterim');
        $this->expectedSideEffect(InForceInterim::class, ['id' => 222], $result2);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                'ValidateApplication',
                'InForceInterim'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    public function testHandleCommandWithVariationAndInterimInForce()
    {
        $command = PayFeeCommand::create(['id' => 111]);

        /** @var Application $application */
        $application = m::mock(Application::class)->makePartial();
        $application->setId(222);
        $application->setIsVariation(false);
        $application->setStatus($this->refData[Application::APPLICATION_STATUS_UNDER_CONSIDERATION]);
        $application->setInterimStatus($this->refData[Application::INTERIM_STATUS_INFORCE]);

        $application
            ->shouldReceive('isGoods')
            ->andReturn(true)
            ->once()
            ->getMock();
        $endResult = new Result();
        $endResult->addMessage('EndInterim');
        $this->expectedSideEffect(EndInterimCmd::class, ['id' => 222], $endResult);

        /** @var FeeEntity $fee */
        $fee = m::mock(FeeEntity::class)->makePartial();
        $fee->setApplication($application);
        $fee->shouldReceive('getFeeType->getFeeType->getId')
            ->andReturn(FeeType::FEE_TYPE_GRANTINT);

        $this->repoMap['Fee']->shouldReceive('fetchUsingId')
            ->with($command)
            ->andReturn($fee);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                'EndInterim'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }
}

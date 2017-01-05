<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Application;

use Dvsa\Olcs\Api\Domain\Command\Application\CreateGrantFee;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\Application\GrantGoods;
use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Mockery as m;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Command\Application\GrantGoods as Cmd;
use Dvsa\Olcs\Api\Domain\Command\Application\CancelAllInterimFees;

/**
 * Grant Goods Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class GrantGoodsTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new GrantGoods();
        $this->mockRepo('Application', \Dvsa\Olcs\Api\Domain\Repository\Application::class);

        $this->mockedSmServices = [
            \ZfcRbac\Service\AuthorizationService::class => m::mock(\ZfcRbac\Service\AuthorizationService::class)
        ];

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            Licence::LICENCE_STATUS_GRANTED,
            ApplicationEntity::APPLICATION_STATUS_GRANTED
        ];
        $this->references = [];

        parent::initReferences();
    }

    public function testHandleCommand()
    {
        $this->setupIsInternalUser(false);

        $data = [
            'id' => 111
        ];

        $command = Cmd::create($data);

        /** @var Licence $licence */
        $licence = m::mock(Licence::class)->makePartial();

        /** @var ApplicationEntity $application */
        $application = m::mock(ApplicationEntity::class)->makePartial();
        $application->setId(111);
        $application->setLicence($licence);

        $this->repoMap['Application']->shouldReceive('fetchUsingId')
            ->with($command)
            ->andReturn($application)
            ->shouldReceive('save')
            ->once()
            ->with($application);

        $result1 = new Result();
        $result1->addMessage('CreateGrantFee');
        $this->expectedSideEffect(CreateGrantFee::class, ['id' => 111], $result1);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                'Application status updated',
                'Licence status updated',
                'CreateGrantFee'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());

        $this->assertEquals(Licence::LICENCE_STATUS_GRANTED, $licence->getStatus()->getId());
        $this->assertEquals(ApplicationEntity::APPLICATION_STATUS_GRANTED, $application->getStatus()->getId());
    }

    public function testHandleCommandCloseTasks()
    {
        $data = [
            'id' => 111
        ];

        $command = Cmd::create($data);

        $this->setupIsInternalUser();

        /** @var Licence $licence */
        $licence = m::mock(Licence::class)->makePartial();

        /** @var ApplicationEntity $application */
        $application = m::mock(ApplicationEntity::class)->makePartial();
        $application->setId(111);
        $application->setLicence($licence);
        $application->setIsVariation(true);

        $this->repoMap['Application']->shouldReceive('fetchUsingId')->with($command)->once()->andReturn($application)
            ->shouldReceive('save')->with($application)->once();

        $this->expectedSideEffect(
            \Dvsa\Olcs\Api\Domain\Command\Application\CloseTexTask::class,
            ['id' => 111],
            (new Result())->addMessage('CLOSE_TEX_TASK')
        );
        $this->expectedSideEffect(
            \Dvsa\Olcs\Api\Domain\Command\Application\CloseFeeDueTask::class,
            ['id' => 111],
            (new Result())->addMessage('CLOSE_FEEDUE_TASK')
        );

        $result1 = new Result();
        $result1->addMessage('CreateGrantFee');
        $this->expectedSideEffect(CreateGrantFee::class, ['id' => 111], $result1);

        $result2 = new Result();
        $result2->addMessage('CancelAllInterimFees');
        $this->expectedSideEffect(CancelAllInterimFees::class, ['id' => 111], $result2);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                'Application status updated',
                'Licence status updated',
                'CLOSE_TEX_TASK',
                'CLOSE_FEEDUE_TASK',
                'CancelAllInterimFees',
                'CreateGrantFee'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }
}

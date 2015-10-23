<?php

/**
 * Read Bus Reg Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Audit;

use Dvsa\Olcs\Api\Domain\CommandHandler\Audit\ReadBusReg;
use Dvsa\Olcs\Api\Entity\Bus\BusReg;
use Dvsa\Olcs\Api\Entity\Bus\BusRegReadAudit;
use Dvsa\Olcs\Api\Entity\User\User;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\Repository;
use ZfcRbac\Service\AuthorizationService;

/**
 * Read Bus Reg Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ReadBusRegTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new ReadBusReg();
        $this->mockRepo('BusRegReadAudit', Repository\BusRegReadAudit::class);
        $this->mockRepo('Bus', Repository\Bus::class);

        $this->mockedSmServices[AuthorizationService::class] = m::mock(AuthorizationService::class);

        parent::setUp();
    }

    public function testHandleCommandWhenExists()
    {
        $data = [
            'id' => '111'
        ];

        $command = \Dvsa\Olcs\Transfer\Command\Audit\ReadBusReg::create($data);

        $user = m::mock(User::class)->makePartial();
        $user->setId(222);

        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('getIdentity->getUser')->andReturn($user);

        $this->repoMap['BusRegReadAudit']->shouldReceive('fetchOne')
            ->once()
            ->with(222, 111, date('Y-m-d'))
            ->andReturn(['foo']);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                'Audit record exists'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    public function testHandleCommand()
    {
        $data = [
            'id' => '111'
        ];

        $command = \Dvsa\Olcs\Transfer\Command\Audit\ReadBusReg::create($data);

        $user = m::mock(User::class)->makePartial();
        $user->setId(222);

        $entity = m::mock(BusReg::class);

        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('getIdentity->getUser')->andReturn($user);

        $this->repoMap['BusRegReadAudit']->shouldReceive('fetchOne')->once()
            ->with(222, 111, date('Y-m-d'))
            ->andReturn(null)
            ->shouldReceive('save')
            ->once()
            ->with(m::type(BusRegReadAudit::class))
            ->andReturnUsing(
                function (BusRegReadAudit $record) use ($user, $entity) {
                    $this->assertSame($user, $record->getUser());
                    $this->assertSame($entity, $record->getBusReg());
                }
            );

        $this->repoMap['Bus']->shouldReceive('fetchById')
            ->with(111)
            ->andReturn($entity);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [],
            'messages' => [
                'Audit record created'
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }
}

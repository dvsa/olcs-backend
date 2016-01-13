<?php

/**
 * Read Case Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Audit;

use Dvsa\Olcs\Api\Domain\CommandHandler\Audit\ReadCase;
use Dvsa\Olcs\Api\Entity\Cases\Cases;
use Dvsa\Olcs\Api\Entity\Cases\CasesReadAudit;
use Dvsa\Olcs\Api\Entity\User\User;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\Repository;
use ZfcRbac\Service\AuthorizationService;

/**
 * Read Case Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ReadCaseTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new ReadCase();
        $this->mockRepo('CasesReadAudit', Repository\CasesReadAudit::class);
        $this->mockRepo('Cases', Repository\Bus::class);

        $this->mockedSmServices[AuthorizationService::class] = m::mock(AuthorizationService::class);

        parent::setUp();
    }

    public function testHandleCommandWhenExists()
    {
        $data = [
            'id' => '111'
        ];

        $command = \Dvsa\Olcs\Transfer\Command\Audit\ReadCase::create($data);

        $user = m::mock(User::class)->makePartial();
        $user->setId(222);

        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('getIdentity->getUser')->andReturn($user);

        $this->repoMap['CasesReadAudit']->shouldReceive('fetchOne')
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

        $command = \Dvsa\Olcs\Transfer\Command\Audit\ReadCase::create($data);

        $user = m::mock(User::class)->makePartial();
        $user->setId(222);

        $entity = m::mock(Cases::class);

        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('getIdentity->getUser')->andReturn($user);

        $this->repoMap['CasesReadAudit']->shouldReceive('fetchOne')->once()
            ->with(222, 111, date('Y-m-d'))
            ->andReturn(null)
            ->shouldReceive('save')
            ->once()
            ->with(m::type(CasesReadAudit::class))
            ->andReturnUsing(
                function (CasesReadAudit $record) use ($user, $entity) {
                    $this->assertSame($user, $record->getUser());
                    $this->assertSame($entity, $record->getCase());
                }
            );

        $this->repoMap['Cases']->shouldReceive('fetchById')
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

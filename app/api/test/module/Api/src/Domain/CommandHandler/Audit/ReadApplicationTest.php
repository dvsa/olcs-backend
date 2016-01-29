<?php

/**
 * Read Application Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Audit;

use Dvsa\Olcs\Api\Domain\CommandHandler\Audit\ReadApplication;
use Dvsa\Olcs\Api\Entity\Application\Application;
use Dvsa\Olcs\Api\Entity\Application\ApplicationReadAudit;
use Dvsa\Olcs\Api\Entity\User\User;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\Repository;
use ZfcRbac\Service\AuthorizationService;

/**
 * Read Application Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ReadApplicationTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new ReadApplication();
        $this->mockRepo('ApplicationReadAudit', Repository\ApplicationReadAudit::class);
        $this->mockRepo('Application', Repository\Application::class);

        $this->mockedSmServices[AuthorizationService::class] = m::mock(AuthorizationService::class);

        parent::setUp();
    }

    public function testHandleCommandWhenExists()
    {
        $data = [
            'id' => '111'
        ];

        $command = \Dvsa\Olcs\Transfer\Command\Audit\ReadApplication::create($data);

        $user = m::mock(User::class)->makePartial();
        $user->setId(222);

        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('getIdentity->getUser')->andReturn($user);

        $this->repoMap['ApplicationReadAudit']->shouldReceive('fetchOne')
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

        $command = \Dvsa\Olcs\Transfer\Command\Audit\ReadApplication::create($data);

        $user = m::mock(User::class)->makePartial();
        $user->setId(222);

        $entity = m::mock(Application::class);

        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('getIdentity->getUser')->andReturn($user);

        $this->repoMap['ApplicationReadAudit']->shouldReceive('fetchOne')->once()
            ->with(222, 111, date('Y-m-d'))
            ->andReturn(null)
            ->shouldReceive('save')
            ->once()
            ->with(m::type(ApplicationReadAudit::class))
            ->andReturnUsing(
                function (ApplicationReadAudit $record) use ($user, $entity) {
                    $this->assertSame($user, $record->getUser());
                    $this->assertSame($entity, $record->getApplication());
                }
            );

        $this->repoMap['Application']->shouldReceive('fetchById')
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

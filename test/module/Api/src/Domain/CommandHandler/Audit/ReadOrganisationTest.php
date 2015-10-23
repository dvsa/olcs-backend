<?php

/**
 * Read Organisation Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Audit;

use Dvsa\Olcs\Api\Domain\CommandHandler\Audit\ReadOrganisation;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation;
use Dvsa\Olcs\Api\Entity\Organisation\OrganisationReadAudit;
use Dvsa\Olcs\Api\Entity\User\User;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\Repository;
use ZfcRbac\Service\AuthorizationService;

/**
 * Read Organisation Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ReadOrganisationTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new ReadOrganisation();
        $this->mockRepo('OrganisationReadAudit', Repository\OrganisationReadAudit::class);
        $this->mockRepo('Organisation', Repository\Organisation::class);

        $this->mockedSmServices[AuthorizationService::class] = m::mock(AuthorizationService::class);

        parent::setUp();
    }

    public function testHandleCommandWhenExists()
    {
        $data = [
            'id' => '111'
        ];

        $command = \Dvsa\Olcs\Transfer\Command\Audit\ReadOrganisation::create($data);

        $user = m::mock(User::class)->makePartial();
        $user->setId(222);

        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('getIdentity->getUser')->andReturn($user);

        $this->repoMap['OrganisationReadAudit']->shouldReceive('fetchOne')
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

        $command = \Dvsa\Olcs\Transfer\Command\Audit\ReadOrganisation::create($data);

        $user = m::mock(User::class)->makePartial();
        $user->setId(222);

        $entity = m::mock(Organisation::class);

        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('getIdentity->getUser')->andReturn($user);

        $this->repoMap['OrganisationReadAudit']->shouldReceive('fetchOne')->once()
            ->with(222, 111, date('Y-m-d'))
            ->andReturn(null)
            ->shouldReceive('save')
            ->once()
            ->with(m::type(OrganisationReadAudit::class))
            ->andReturnUsing(
                function (OrganisationReadAudit $record) use ($user, $entity) {
                    $this->assertSame($user, $record->getUser());
                    $this->assertSame($entity, $record->getOrganisation());
                }
            );

        $this->repoMap['Organisation']->shouldReceive('fetchById')
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

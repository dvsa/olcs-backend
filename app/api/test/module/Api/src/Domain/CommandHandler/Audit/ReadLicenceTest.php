<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Audit;

use Dvsa\Olcs\Api\Domain\CommandHandler\Audit\ReadLicence;
use Dvsa\Olcs\Api\Domain\Repository;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\Licence\LicenceReadAudit;
use Dvsa\Olcs\Api\Entity\User\User;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;
use ZfcRbac\Service\AuthorizationService;

/**
 * Read Licence Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class ReadLicenceTest extends CommandHandlerTestCase
{
    const USER_ID = 9999;

    /** @var m\MockInterface|User */
    private $mockUser;
    /** @var  \Dvsa\Olcs\Transfer\Command\Audit\ReadLicence */
    private $command;

    public function setUp()
    {
        $this->sut = new ReadLicence();
        $this->mockRepo('LicenceReadAudit', Repository\LicenceReadAudit::class);
        $this->mockRepo('Licence', Repository\Licence::class);

        $this->mockUser = m::mock(User::class)->makePartial();
        $this->mockUser->setId(self::USER_ID);

        $mockAuthSrv = m::mock(AuthorizationService::class);
        $mockAuthSrv->shouldReceive('getIdentity->getUser')->andReturn($this->mockUser);
        $this->mockedSmServices[AuthorizationService::class] = $mockAuthSrv;

        //  command
        $data = [
            'id' => '111',
        ];
        $this->command = \Dvsa\Olcs\Transfer\Command\Audit\ReadLicence::create($data);

        parent::setUp();
    }

    public function testHandleCommandWhenExists()
    {
        $this->repoMap['LicenceReadAudit']->shouldReceive('fetchOne')
            ->once()
            ->with(self::USER_ID, 111, \DateTime::class)
            ->andReturn(['foo']);

        $result = $this->sut->handleCommand($this->command);

        $expected = [
            'id' => [],
            'messages' => [
                'Audit record exists',
            ],
        ];

        static::assertEquals($expected, $result->toArray());
    }

    public function testHandleCommand()
    {
        $entity = m::mock(Licence::class);

        $this->repoMap['LicenceReadAudit']->shouldReceive('fetchOne')->once()
            ->with(self::USER_ID, 111, \DateTime::class)
            ->andReturn(null)
            ->shouldReceive('save')
            ->once()
            ->with(m::type(LicenceReadAudit::class))
            ->andReturnUsing(
                function (LicenceReadAudit $record) use ($entity) {
                    static::assertSame($this->mockUser, $record->getUser());
                    static::assertSame($entity, $record->getLicence());
                }
            );

        $this->repoMap['Licence']->shouldReceive('fetchById')
            ->with(111)
            ->andReturn($entity);

        $result = $this->sut->handleCommand($this->command);

        $expected = [
            'id' => [],
            'messages' => [
                'Audit record created',
            ],
        ];

        static::assertEquals($expected, $result->toArray());
    }
}

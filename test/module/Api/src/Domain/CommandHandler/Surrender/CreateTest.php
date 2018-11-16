<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Surrender;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\Surrender\Create as Sut;
use Dvsa\Olcs\Api\Domain\Repository\Query\Licence as LicenceRepo;
use Dvsa\Olcs\Transfer\Command\Surrender\Create as Cmd;
use Dvsa\Olcs\Api\Domain\Repository\Surrender as SurrenderRepo;
use Dvsa\Olcs\Api\Entity\Surrender as SurrenderEntity;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;

class CreateTest extends CommandHandlerTestCase
{
    const LIC_ID = 111;

    /** @var Sut */
    protected $sut;

    public function setUp()
    {
        $this->sut = new Sut();

        $this->mockRepo('Surrender', SurrenderRepo::class);
        $this->mockRepo('Licence', LicenceRepo::class);
        $this->mockedSmServices = [
            \ZfcRbac\Service\AuthorizationService::class => m::mock(\ZfcRbac\Service\AuthorizationService::class)
        ];
        parent::setUp();
    }

    public function testHandleCommand()
    {
        $data = [
            'id' => self::LIC_ID,
        ];

        $command = Cmd::create($data);

        $licence = $this->getTestingLicence();

        $this->repoMap['Licence']
            ->shouldReceive('fetchById')
            ->with(self::LIC_ID)
            ->once()
            ->andReturn($licence);

        $this->repoMap['Surrender']
            ->shouldReceive('save')
            ->with(m::type(SurrenderEntity::class))
            ->once();

        $result = $this->sut->handleCommand($command);

        $this->assertSame(['Surrender successfully created.'], $result->getMessages());

        $this->assertInstanceOf(Result::class, $result);
    }
}

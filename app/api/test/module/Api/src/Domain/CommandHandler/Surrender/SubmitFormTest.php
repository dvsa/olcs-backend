<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Surrender;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\Surrender;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Transfer\Command\Surrender\SubmitForm as Cmd;
use Dvsa\Olcs\Api\Domain\CommandHandler\Surrender\SubmitForm as sut;
use Mockery as m;

class SubmitFormTest extends CommandHandlerTestCase
{
    /** @var Sut */
    protected $sut;

    public function setUp()
    {
        $this->sut = new Sut();
        $this->refData = [];
        $this->mockRepo('Surrender', \Dvsa\Olcs\Api\Domain\Repository\Surrender::class);
        $this->mockRepo('Licence', \Dvsa\Olcs\Api\Domain\Repository\Licence::class);
        $this->mockedSmServices = [
            \ZfcRbac\Service\AuthorizationService::class => m::mock(\ZfcRbac\Service\AuthorizationService::class)
        ];
        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            RefData::SIG_PHYSICAL_SIGNATURE,
        ];

        parent::initReferences();
    }

    public function testHandleCommand()
    {
        $data = [
            'id' => 65,
        ];
        $command = Cmd::create($data);

        $this->expectedSideEffect(
            \Dvsa\Olcs\Transfer\Command\Surrender\Update::class,
            [
                'signatureType' => RefData::SIG_PHYSICAL_SIGNATURE,
                'id' => 65,
                'status' => Surrender::SURRENDER_STATUS_SIGNED,
            ],
            new Result()
        );

        $licence = m::mock(Licence::class)
            ->shouldReceive('setStatus')
            ->once()
            ->getMock();


        $this->repoMap['Licence']
            ->shouldReceive('fetchById')
            ->with(65)
            ->once()
            ->andReturn($licence);

        $this->repoMap['Licence']
            ->shouldReceive('save')
            ->with($licence)
            ->once();

        $this->sut->handleCommand($command);
    }
}

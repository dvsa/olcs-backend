<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Surrender;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\Surrender\Withdraw as WithdrawHandler;
use Dvsa\Olcs\Api\Domain\Repository\Query\Licence as LicenceRepo;
use Dvsa\Olcs\Api\Domain\Repository\Surrender as SurrenderRepo;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Transfer\Command\Surrender\Update as UpdateCommand;
use Dvsa\Olcs\Transfer\Command\Surrender\Withdraw as WithdrawCommand;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;
use ZfcRbac\Service\AuthorizationService;

class WithdrawTest extends CommandHandlerTestCase
{
    /**
     * @var WithdrawHandler
     */
    protected $sut;

    public function setUp()
    {
        $this->sut = new WithdrawHandler();
        $this->mockRepo('Surrender', SurrenderRepo::class);
        $this->mockRepo('Licence', LicenceRepo::class);
        $this->refData = [];
        $this->mockedSmServices = [
            AuthorizationService::class => m::mock(AuthorizationService::class)
        ];
        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            RefData::SURRENDER_STATUS_WITHDRAWN,
        ];

        parent::initReferences();
    }

    public function testHandleCommand()
    {
        $licenceId = 111;

        $data = [
            'id' => $licenceId,
        ];

        $command = WithdrawCommand::create($data);

        $this->expectedSideEffect(
            UpdateCommand::class,
            [
                'id' => $licenceId,
                'status' => RefData::SURRENDER_STATUS_WITHDRAWN,
            ],
            new Result()
        );

        $this->queryHandler
            ->shouldReceive('handleQuery')
            ->once()
            ->andReturn(['status' => 'licence_status']);

        $licence = $this->getTestingLicence();
        $this->repoMap['Licence']
            ->shouldReceive('fetchById')
            ->with($licenceId)
            ->once()
            ->andReturn($licence);

        $this->repoMap['Licence']
            ->shouldReceive('save')
            ->with(m::type(LicenceEntity::class))
            ->once();

        $this->sut->handleCommand($command);
    }
}

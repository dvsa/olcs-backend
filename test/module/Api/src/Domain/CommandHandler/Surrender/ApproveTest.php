<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Surrender;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\Surrender\Approve as ApproveHandler;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Transfer\Command\Licence\SurrenderLicence;
use Dvsa\Olcs\Transfer\Command\Surrender\Approve as ApproveCommand;
use Dvsa\Olcs\Transfer\Command\Surrender\Update as UpdateCommand;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;

class ApproveTest extends CommandHandlerTestCase
{
    /**
     * @var ApproveHandler
     */
    protected $sut;

    public function setUp()
    {
        $this->sut = new ApproveHandler();
        $this->refData = [];
        $this->mockedSmServices = [
            \ZfcRbac\Service\AuthorizationService::class => m::mock(\ZfcRbac\Service\AuthorizationService::class)
        ];
        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            RefData::SURRENDER_STATUS_APPROVED,
        ];

        parent::initReferences();
    }

    public function testHandleCommand()
    {

        $now = new \DateTime();
        $data = [
            'id' => 45,
            'surrenderDate' => $now->format('Y-m-d')
        ];

        $command = ApproveCommand::create($data);

        $this->expectedSideEffect(
            UpdateCommand::class,
            [
                'id' => 45,
                'status' => RefData::SURRENDER_STATUS_APPROVED,
            ],
            new Result()
        );

        $this->expectedSideEffect(
            SurrenderLicence::class,
            [
                'id' => 45,
                'surrenderDate' => $data['surrenderDate'],
                'terminated' => false
            ],
            new Result()
        );

        $this->sut->handleCommand($command);
    }
}

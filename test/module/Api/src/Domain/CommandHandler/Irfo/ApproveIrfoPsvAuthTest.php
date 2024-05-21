<?php

/**
 * Approve Irfo Psv Auth Test
 */

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Irfo;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\Irfo\ApproveIrfoPsvAuth as Sut;
use Dvsa\Olcs\Api\Domain\Repository\IrfoPsvAuth as IrfoPsvAuthRepo;
use Dvsa\Olcs\Api\Domain\Repository\Fee as FeeRepo;
use Dvsa\Olcs\Api\Entity\Irfo\IrfoPsvAuth as IrfoPsvAuthEntity;
use Dvsa\Olcs\Api\Entity\Irfo\IrfoPsvAuthType;
use Dvsa\Olcs\Transfer\Command\Irfo\ApproveIrfoPsvAuth as Cmd;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\AbstractCommandHandlerTestCase;
use Mockery as m;

/**
 * Approve Irfo Psv Auth Test
 */
class ApproveIrfoPsvAuthTest extends AbstractCommandHandlerTestCase
{
    public function setUp(): void
    {
        $this->sut = new Sut();
        $this->mockRepo('IrfoPsvAuth', IrfoPsvAuthRepo::class);
        $this->mockRepo('Fee', FeeRepo::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            IrfoPsvAuthEntity::JOURNEY_FREQ_DAILY,
            IrfoPsvAuthEntity::STATUS_APPROVED,
        ];

        $this->references = [
            IrfoPsvAuthType::class => [
                22 => m::mock(IrfoPsvAuthType::class)
            ],
        ];

        parent::initReferences();
    }

    /**
     * testHandleCommand
     */
    public function testHandleCommand()
    {
        $id = 99;

        $data = [
            'id' => $id,
            'version' => 2,
            'irfoPsvAuthType' => 22,
            'journeyFrequency' => IrfoPsvAuthEntity::JOURNEY_FREQ_DAILY,
            'irfoPsvAuthNumbers' => [],
            'inForceDate' => '2015-01-01',
        ];

        $command = Cmd::create($data);

        $this->repoMap['Fee']->shouldReceive('fetchFeesByIrfoPsvAuthId')
            ->with($id, true)
            ->andReturn(['FEE']);

        /** @var IrfoPsvAuthEntity $irfoPsvAuth */
        $irfoPsvAuth = m::mock(IrfoPsvAuthEntity::class)->makePartial();
        $irfoPsvAuth->setIrfoPsvAuthNumbers([]);
        $irfoPsvAuth
            ->shouldReceive('update')
            ->once()
            ->shouldReceive('approve')
            ->once()
            ->with($this->refData[IrfoPsvAuthEntity::STATUS_APPROVED], ['FEE'])
            ->shouldReceive('getId')
            ->andReturn($id);

        $this->repoMap['IrfoPsvAuth']->shouldReceive('fetchUsingId')
            ->with($command, Query::HYDRATE_OBJECT, 2)
            ->andReturn($irfoPsvAuth)
            ->shouldReceive('save')
            ->with(m::type(IrfoPsvAuthEntity::class))
            ->once();

        $result = $this->sut->handleCommand($command);

        $this->assertInstanceOf(Result::class, $result);
    }
}

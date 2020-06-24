<?php

/**
 * Approve Irfo Gv Permit Test
 */
namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Irfo;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\Irfo\ApproveIrfoGvPermit as Sut;
use Dvsa\Olcs\Api\Domain\Repository\IrfoGvPermit as IrfoGvPermitRepo;
use Dvsa\Olcs\Api\Domain\Repository\Fee as FeeRepo;
use Dvsa\Olcs\Api\Entity\Irfo\IrfoGvPermit as IrfoGvPermitEntity;
use Dvsa\Olcs\Transfer\Command\Irfo\ApproveIrfoGvPermit as Cmd;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;

/**
 * Approve Irfo Gv Permit Test
 */
class ApproveIrfoGvPermitTest extends CommandHandlerTestCase
{
    public function setUp(): void
    {
        $this->sut = new Sut();
        $this->mockRepo('IrfoGvPermit', IrfoGvPermitRepo::class);
        $this->mockRepo('Fee', FeeRepo::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            IrfoGvPermitEntity::STATUS_APPROVED,
        ];

        parent::initReferences();
    }

    /**
     * testHandleCommand
     */
    public function testHandleCommand()
    {
        $id = 99;

        $fees = [m::mock(FeeEntity::class)];

        $command = Cmd::Create(
            [
                'id' => $id,
            ]
        );

        /** @var IrfoGvPermitEntity $irfoGvPermit */
        $irfoGvPermit = m::mock(IrfoGvPermitEntity::class);
        $irfoGvPermit->shouldReceive('approve')
            ->once()
            ->with($this->refData[IrfoGvPermitEntity::STATUS_APPROVED], $fees)
            ->shouldReceive('getId')
            ->andReturn($id);

        $this->repoMap['IrfoGvPermit']->shouldReceive('fetchUsingId')
            ->with($command, Query::HYDRATE_OBJECT)
            ->andReturn($irfoGvPermit)
            ->shouldReceive('save')
            ->with(m::type(IrfoGvPermitEntity::class))
            ->once();

        $this->repoMap['Fee']->shouldReceive('fetchFeesByIrfoGvPermitId')
            ->with($id)
            ->andReturn($fees);

        $result = $this->sut->handleCommand($command);

        $this->assertInstanceOf(Result::class, $result);
    }
}

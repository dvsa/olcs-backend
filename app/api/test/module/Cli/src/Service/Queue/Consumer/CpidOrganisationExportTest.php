<?php

namespace Dvsa\OlcsTest\Cli\Service\Queue\Consumer;

use Doctrine\ORM\Internal\Hydration\IterableResult;
use Dvsa\Olcs\Api\Domain\CommandHandlerManager;
use Dvsa\Olcs\Api\Domain\Repository;
use Dvsa\Olcs\Api\Entity;
use Dvsa\Olcs\Api\Entity\Queue\Queue;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Cli\Service\Queue\Consumer\CpidOrganisationExport;
use Dvsa\Olcs\Transfer\Command as TransferCmd;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * @covers Dvsa\Olcs\Cli\Service\Queue\Consumer\CpidOrganisationExport
 */
class CpidOrganisationExportTest extends MockeryTestCase
{
    protected $queueEntity = null;

    public function setUp(): void
    {
        $user = new Entity\User\User('pid', 'type');
        $user->setId(1);

        $this->queueEntity = (new Queue())
            ->setType(new RefData(Queue::TYPE_CPID_EXPORT_CSV))
            ->setStatus(new RefData(Queue::STATUS_QUEUED))
            ->setCreatedBy($user)
            ->setOptions(json_encode(['status' => 'unit_Status']));
    }

    /**
     * @dataProvider dpTestMessageProvider
     */
    public function testProcessMessage($shouldThrowException)
    {
        $row = ['A1', 'B1', 'C1'];

        /** @var Repository\Organisation $organisation */
        $organisation = m::mock(Repository\Organisation::class)
            ->shouldReceive('fetchAllByStatusForCpidExport')
            ->with('unit_Status')
            ->andReturn(
                m::mock(IterableResult::class)
                    ->makePartial()
                    ->shouldReceive('next')
                    ->twice()
                    ->andReturn([$row], false)
                    ->getMock()
            )
            ->getMock();

        /** @var m\MockInterface $mockCmdHandlerMngr */
        $mockCmdHandlerMngr = m::mock(CommandHandlerManager::class);

        /** @var m\MockInterface $sut */
        $sut = m::mock(CpidOrganisationExport::class . '[success, failed]', [$organisation, $mockCmdHandlerMngr])
            ->shouldAllowMockingProtectedMethods();

        if ($shouldThrowException) {
            $mockCmdHandlerMngr->shouldReceive('handleCommand')
                ->once()
                ->andThrow(new \Exception('AN EXCEPTION'));

            $expectResult = 'failed';

            $sut->shouldReceive('failed')
                ->once()
                ->with($this->queueEntity, 'Unable to export list. AN EXCEPTION')
                ->andReturn($expectResult);

        } else {
            $mockCmdHandlerMngr->shouldReceive('handleCommand')
                ->once()
                ->andReturnUsing(
                    function (TransferCmd\Document\Upload $cmd) {
                        static::assertEquals(base64_encode("A1,B1,C1\n"), $cmd->getContent());
                    }
                );

            $expectResult = 'success';

            $sut->shouldReceive('success')
                ->once()
                ->with($this->queueEntity, 'Organisation list exported.')
                ->andReturn($expectResult);
        }

        static::assertEquals(
            $sut->processMessage($this->queueEntity),
            $expectResult
        );
    }

    public function dpTestMessageProvider()
    {
        return [
            [
                'shouldThrowException' => false,
            ],
            [
                'shouldThrowException' => true,
            ],
        ];
    }
}

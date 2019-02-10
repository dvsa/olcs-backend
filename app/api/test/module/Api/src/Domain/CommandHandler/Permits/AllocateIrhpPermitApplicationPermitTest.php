<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\Permits;

use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\Permits\AllocateIrhpPermitApplicationPermit;
use Dvsa\Olcs\Api\Domain\Command\Permits\AllocateIrhpPermitApplicationPermit as Cmd;
use Dvsa\Olcs\Api\Entity\IrhpInterface;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitRange;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermit;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitApplication as IrhpPermitApplicationRepo;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermit as IrhpPermitRepo;
use Mockery as m;
use RuntimeException;

class AllocateIrhpPermitApplicationPermitTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->mockRepo('IrhpPermitApplication', IrhpPermitApplicationRepo::class);
        $this->mockRepo('IrhpPermit', IrhpPermitRepo::class);
        $this->sut = new AllocateIrhpPermitApplicationPermit();
     
        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            IrhpInterface::STATUS_VALID,
            IrhpPermit::STATUS_PENDING
        ];

        parent::initReferences();
    }

    public function testAllocatePermitInFirstRange()
    {
        $irhpPermitApplicationId = 305;

        $irhpPermitRange1Id = 100;
        $irhpPermitRange1AssignedNumbers = [500, 501, 503];
        $irhpPermitRange1 = $this->createMockRange($irhpPermitRange1Id, 500, 504, 5);

        $this->repoMap['IrhpPermit']->shouldReceive('getAssignedPermitNumbersByRange')
            ->with($irhpPermitRange1Id)
            ->andReturn($irhpPermitRange1AssignedNumbers);

        $irhpPermitRanges = [$irhpPermitRange1];

        $irhpPermitApplication = m::mock(IrhpPermitApplication::class);
        $irhpPermitApplication->shouldReceive('getId')
            ->andReturn($irhpPermitApplicationId);
        $irhpPermitApplication->shouldReceive(
            'getIrhpPermitWindow->getIrhpPermitStock->getNonReservedNonReplacementRangesOrderedByFromNo'
        )
        ->andReturn($irhpPermitRanges);

        $this->repoMap['IrhpPermitApplication']->shouldReceive('fetchById')
            ->with($irhpPermitApplicationId)
            ->andReturn($irhpPermitApplication);

        $this->repoMap['IrhpPermit']->shouldReceive('save')
            ->once()
            ->with(m::on(function ($irhpPermit) use ($irhpPermitApplication, $irhpPermitRange1) {
                $this->assertSame($irhpPermitApplication, $irhpPermit->getIrhpPermitApplication());
                $this->assertSame($irhpPermitRange1, $irhpPermit->getIrhpPermitRange());
                $this->assertEquals(502, $irhpPermit->getPermitNumber());
                $this->assertSame($this->refData[IrhpPermit::STATUS_PENDING], $irhpPermit->getStatus());
                return true;
            }));

        $command = m::mock(Cmd::class);
        $command->shouldReceive('getId')
            ->andReturn($irhpPermitApplicationId);

        $result = $this->sut->handleCommand($command);

        $this->assertEquals(
            ['Allocated permit number 502 in range 100 for irhp permit application 305'],
            $result->getMessages()
        );
    }

    public function testFirstRangeFullAllocatePermitInSecondRange()
    {
        $irhpPermitApplicationId = 400;

        $irhpPermitRange1Id = 101;
        $irhpPermitRange1AssignedNumbers = [504, 502, 500, 501, 503];
        $irhpPermitRange1 = $this->createMockRange($irhpPermitRange1Id, 500, 504, 5);

        $this->repoMap['IrhpPermit']->shouldReceive('getAssignedPermitNumbersByRange')
            ->with($irhpPermitRange1Id)
            ->andReturn($irhpPermitRange1AssignedNumbers);

        $irhpPermitRange2Id = 102;
        $irhpPermitRange2AssignedNumbers = [758, 757, 750, 751, 755];
        $irhpPermitRange2 = $this->createMockRange($irhpPermitRange2Id, 750, 758, 9);

        $this->repoMap['IrhpPermit']->shouldReceive('getAssignedPermitNumbersByRange')
            ->with($irhpPermitRange2Id)
            ->andReturn($irhpPermitRange2AssignedNumbers);

        $irhpPermitRanges = [$irhpPermitRange1, $irhpPermitRange2];

        $irhpPermitApplication = m::mock(IrhpPermitApplication::class);
        $irhpPermitApplication->shouldReceive('getId')
            ->andReturn($irhpPermitApplicationId);
        $irhpPermitApplication->shouldReceive(
            'getIrhpPermitWindow->getIrhpPermitStock->getNonReservedNonReplacementRangesOrderedByFromNo'
        )
        ->andReturn($irhpPermitRanges);

        $this->repoMap['IrhpPermitApplication']->shouldReceive('fetchById')
            ->with($irhpPermitApplicationId)
            ->andReturn($irhpPermitApplication);

        $this->repoMap['IrhpPermit']->shouldReceive('save')
            ->once()
            ->with(m::on(function ($irhpPermit) use ($irhpPermitApplication, $irhpPermitRange2) {
                $this->assertSame($irhpPermitApplication, $irhpPermit->getIrhpPermitApplication());
                $this->assertSame($irhpPermitRange2, $irhpPermit->getIrhpPermitRange());
                $this->assertEquals(752, $irhpPermit->getPermitNumber());
                $this->assertSame($this->refData[IrhpPermit::STATUS_PENDING], $irhpPermit->getStatus());
                return true;
            }));

        $command = m::mock(Cmd::class);
        $command->shouldReceive('getId')
            ->andReturn($irhpPermitApplicationId);

        $result = $this->sut->handleCommand($command);

        $this->assertEquals(
            ['Allocated permit number 752 in range 102 for irhp permit application 400'],
            $result->getMessages()
        );
    }

    public function testExceptionOnAllRangesFull()
    {
        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Unable to find range with free permits for irhp permit application 400');

        $irhpPermitApplicationId = 400;

        $irhpPermitRange1Id = 101;
        $irhpPermitRange1AssignedNumbers = [504, 502, 500, 501, 503];
        $irhpPermitRange1 = $this->createMockRange($irhpPermitRange1Id, 500, 504, 5);

        $this->repoMap['IrhpPermit']->shouldReceive('getAssignedPermitNumbersByRange')
            ->with($irhpPermitRange1Id)
            ->andReturn($irhpPermitRange1AssignedNumbers);

        $irhpPermitRange2Id = 102;
        $irhpPermitRange2AssignedNumbers = [758, 757, 750, 751, 755, 752, 753, 754, 756];
        $irhpPermitRange2 = $this->createMockRange($irhpPermitRange2Id, 750, 758, 9);

        $this->repoMap['IrhpPermit']->shouldReceive('getAssignedPermitNumbersByRange')
            ->with($irhpPermitRange2Id)
            ->andReturn($irhpPermitRange2AssignedNumbers);

        $irhpPermitRanges = [$irhpPermitRange1, $irhpPermitRange2];

        $irhpPermitApplication = m::mock(IrhpPermitApplication::class);
        $irhpPermitApplication->shouldReceive('getId')
            ->andReturn($irhpPermitApplicationId);
        $irhpPermitApplication->shouldReceive(
            'getIrhpPermitWindow->getIrhpPermitStock->getNonReservedNonReplacementRangesOrderedByFromNo'
        )
        ->andReturn($irhpPermitRanges);

        $this->repoMap['IrhpPermitApplication']->shouldReceive('fetchById')
            ->with($irhpPermitApplicationId)
            ->andReturn($irhpPermitApplication);

        $command = m::mock(Cmd::class);
        $command->shouldReceive('getId')
            ->andReturn($irhpPermitApplicationId);

        $this->sut->handleCommand($command);
    }

    private function createMockRange($id, $fromNo, $toNo, $size)
    {
        $irhpPermitRange = m::mock(IrhpPermitRange::class);
        $irhpPermitRange->shouldReceive('getId')
            ->andReturn($id);
        $irhpPermitRange->shouldReceive('getFromNo')
            ->andReturn($fromNo);
        $irhpPermitRange->shouldReceive('getToNo')
            ->andReturn($toNo);
        $irhpPermitRange->shouldReceive('getSize')
            ->andReturn($size);

        return $irhpPermitRange;
    }
}

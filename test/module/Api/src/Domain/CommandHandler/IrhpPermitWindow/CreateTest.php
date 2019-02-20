<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\IrhpPermitWindow;

use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitType;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\CommandHandler\IrhpPermitWindow\Create as CreateHandler;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitStock as PermitStockRepo;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitWindow as PermitWindowRepo;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Transfer\Command\IrhpPermitWindow\Create as CreateCmd;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitWindow as PermitWindowEntity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitStock;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;

/**
 * Create IRHP Permit Window Test
 *
 * @author Scott Callaway <scott.callaway@capgemini.com>
 */
class CreateTest extends CommandHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new CreateHandler();
        $this->mockRepo('IrhpPermitWindow', PermitWindowRepo::class);
        $this->mockRepo('IrhpPermitStock', PermitStockRepo::class);

        $this->today = (new DateTime())->format('Y-m-d');
        $this->tomorrow = (new DateTime)->modify('+1 day')->format('Y-m-d');
        $this->yesterday = (new DateTime)->modify('-1 day')->format('Y-m-d');

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            PermitWindowEntity::EMISSIONS_CATEGORY_EURO5_REF,
            PermitWindowEntity::EMISSIONS_CATEGORY_EURO6_REF,
            PermitWindowEntity::EMISSIONS_CATEGORY_NA_REF
        ];

        parent::initReferences();
    }

    public function testHandleCommand()
    {
        $cmdData = [
            'irhpPermitStock' => '1',
            'startDate' => $this->today,
            'endDate' => $this->tomorrow,
            'daysForPayment' => '14',
            'emissionsCategory' => PermitWindowEntity::EMISSIONS_CATEGORY_EURO6_REF
        ];

        $this->repoMap['IrhpPermitWindow']
            ->shouldReceive('findOverlappingWindowsByType')
            ->once()
            ->andReturn([]);

        $command = CreateCmd::create($cmdData);

        $irhpPermitStock = m::mock(IrhpPermitStock::class);

        $this->repoMap['IrhpPermitStock']
            ->shouldReceive('fetchById')
            ->with($command->getIrhpPermitStock())
            ->andReturn($irhpPermitStock);

        $irhpPermitStock->shouldReceive('getIrhpPermitType->isEcmtAnnual')->once()->andReturn(true);

        $this->repoMap['IrhpPermitWindow']
            ->shouldReceive('save')
            ->once()
            ->with(m::type(PermitWindowEntity::class))
            ->andReturnUsing(
                function (PermitWindowEntity $permitWindow) use (&$savedPermitStock) {
                    $permitWindow->setId(1);
                }
            );

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => ['IrhpPermitWindow' => 1],
            'messages' => ["IRHP Permit Window '1' created"]
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    public function testHandleCommandBadEcmtEmissionsCategory()
    {
        $cmdData = [
            'irhpPermitStock' => '1',
            'startDate' => $this->today,
            'endDate' => $this->tomorrow,
            'daysForPayment' => '14',
            'emissionsCategory' => PermitWindowEntity::EMISSIONS_CATEGORY_NA_REF
        ];

        $this->repoMap['IrhpPermitWindow']
            ->shouldReceive('findOverlappingWindowsByType')
            ->once()
            ->andReturn([]);

        $command = CreateCmd::create($cmdData);

        $irhpPermitStock = m::mock(IrhpPermitStock::class);

        $this->repoMap['IrhpPermitStock']
            ->shouldReceive('fetchById')
            ->with($command->getIrhpPermitStock())
            ->andReturn($irhpPermitStock);

        $irhpPermitStock->shouldReceive('getIrhpPermitType->isEcmtAnnual')->once()->andReturn(true);

        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Emissions Category: N/A not valid for Annual ECMT Stock');

        $this->sut->handleCommand($command);
    }

    /**
     * @expectedException \Dvsa\Olcs\Api\Domain\Exception\ValidationException
     * @expectedExceptionMessage The dates overlap with another window for this Permit stock
     *
     * Test for overlapping IRHP Permit Windows - no values are asserted as this tests to ensure that a validation
     * exception is thrown.
     */
    public function testHandleOverlap()
    {
        $cmdData = [
            'irhpPermitStock' => '1',
            'startDate' => $this->today,
            'endDate' => $this->tomorrow,
            'daysForPayment' => '14'
        ];

        $command = CreateCmd::create($cmdData);

        $this->repoMap['IrhpPermitWindow']
            ->shouldReceive('findOverlappingWindowsByType')
            ->once()
            ->with(
                $cmdData['irhpPermitStock'],
                $cmdData['startDate'],
                $cmdData['endDate'],
                null
            )
            ->andReturn([m::mock(PermitWindowEntity::class)]);

        $this->sut->handleCommand($command);
    }
}

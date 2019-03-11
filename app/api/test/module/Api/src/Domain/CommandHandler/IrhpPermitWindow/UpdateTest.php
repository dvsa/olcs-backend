<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\IrhpPermitWindow;

use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitType;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\CommandHandler\IrhpPermitWindow\Update as UpdateHandler;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitStock as PermitStockRepo;
use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitWindow as PermitWindowRepo;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Dvsa\Olcs\Transfer\Command\IrhpPermitWindow\Update as UpdateCmd;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitWindow as PermitWindowEntity;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitStock as PermitStockEntity;
use Dvsa\Olcs\Api\Entity\Traits\ProcessDateTrait;

/**
 * Update IrhpPermitStock Test
 *
 * @author Andy Newton <andy@vitri.ltd>
 */
class UpdateTest extends CommandHandlerTestCase
{
    use ProcessDateTrait;

    public function setUp()
    {
        $this->sut = new UpdateHandler();
        $this->mockRepo('IrhpPermitWindow', PermitWindowRepo::class);
        $this->mockRepo('IrhpPermitStock', PermitStockRepo::class);

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

    // Happy Path
    public function testHandleCommand()
    {
        $cmdData = [
            'id' => 1,
            'irhpPermitStock' => 1,
            'startDate' => '2019-12-01',
            'endDate' => '2019-12-30',
            'daysForPayment' => 14,
            'emissionsCategory' => PermitWindowEntity::EMISSIONS_CATEGORY_EURO6_REF
        ];

        $command = UpdateCmd::create($cmdData);

        $permitWindowEntity = m::mock(PermitWindowEntity::class);
        $permitStockEntity = m::mock(PermitStockEntity::class);

        $this->repoMap['IrhpPermitWindow']
            ->shouldReceive('fetchById')
            ->with($cmdData['id'])
            ->once()
            ->andReturn($permitWindowEntity);

        $permitWindowEntity->shouldReceive('getIrhpPermitStock->getIrhpPermitType->isEcmtAnnual')->once()->andReturn(true);

        $permitWindowEntity
            ->shouldReceive('hasEnded')
            ->once()
            ->andReturn(false);

        $permitWindowEntity
            ->shouldReceive('isActive')
            ->once()
            ->andReturn(false);

        $this->repoMap['IrhpPermitWindow']
            ->shouldReceive('findOverlappingWindowsByType')
            ->once()
            ->andReturn([]);

        $this->repoMap['IrhpPermitStock']
            ->shouldReceive('fetchById')
            ->with($cmdData['irhpPermitStock'])
            ->once()
            ->andReturn($permitStockEntity);

        $permitWindowEntity
            ->shouldReceive('update')
            ->once()
            ->with(
                $permitStockEntity,
                m::type(RefData::class),
                $cmdData['startDate'],
                $cmdData['endDate'],
                $cmdData['daysForPayment']
            );

        $this->repoMap['IrhpPermitWindow']
            ->shouldReceive('save')
            ->once()
            ->with($permitWindowEntity);

        $permitWindowEntity
            ->shouldReceive('getId')
            ->twice()
            ->andReturn($cmdData['id']);

        $result = $this->sut->handleCommand($command);

        $expected = [
            'id' => [
                'Irhp Permit Window' => 1
            ],
            'messages' => [
                sprintf(
                    "Irhp Permit Window '%d' Updated",
                    $cmdData['id']
                )
            ]
        ];

        $this->assertEquals($expected, $result->toArray());
    }

    /**
     * @expectedException \Dvsa\Olcs\Api\Domain\Exception\ValidationException
     * @expectedExceptionMessage Windows which have ended cannot be edited
     *
     * Test for ended Window - no values are asserted as this tests to ensure that a validation exception is thrown.
     */
    public function testHandleWindowEnd()
    {
        $cmdData = [
            'id' => 1,
            'irhpPermitStock' => 1,
            'startDate' => '2017-12-01',
            'endDate' => '2017-12-30',
            'daysForPayment' => 14
        ];

        $command = UpdateCmd::create($cmdData);

        $permitWindowEntity = m::mock(PermitWindowEntity::class);

        $this->repoMap['IrhpPermitWindow']
            ->shouldReceive('fetchById')
            ->with($cmdData['id'])
            ->andReturn($permitWindowEntity);

        $permitWindowEntity
            ->shouldReceive('hasEnded')
            ->once()
            ->andReturn(true);

        $this->sut->handleCommand($command);
    }

    /**
     * @expectedException \Dvsa\Olcs\Api\Domain\Exception\ValidationException
     * @expectedExceptionMessage It is not permitted to edit the start date of an Active Window
     *
     * Test to prevent editing the start date of an active window - no values are asserted as this tests to ensure that
     * a validation exception is thrown.
     */
    public function testIsActiveEditStartDate()
    {
        $cmdData = [
            'id' => 1,
            'irhpPermitStock' => 1,
            'startDate' => '2017-12-01',
            'endDate' => '2017-12-30',
            'daysForPayment' => 14,
            'emissionsCategory' => PermitWindowEntity::EMISSIONS_CATEGORY_EURO6_REF
        ];

        $command = UpdateCmd::create($cmdData);

        $permitWindowEntity = m::mock(PermitWindowEntity::class);

        $permitWindowEntity->shouldReceive('getIrhpPermitStock->getIrhpPermitType->isEcmtAnnual')->once()->andReturn(true);

        $this->repoMap['IrhpPermitWindow']
            ->shouldReceive('fetchById')
            ->with($cmdData['id'])
            ->andReturn($permitWindowEntity);

        $permitWindowEntity
            ->shouldReceive('hasEnded')
            ->once()
            ->andReturn(false);
        $permitWindowEntity
            ->shouldReceive('isActive')
            ->andReturn(true);

        $permitWindowEntity
            ->shouldReceive('getStartDate')
            ->once()
            ->andReturn('2017-10-01');

        $this->sut->handleCommand($command);
    }

    /**
     * @expectedException \Dvsa\Olcs\Api\Domain\Exception\ValidationException
     * @expectedExceptionMessage The end date of an Active Window must be greater than or equal to todays date
     *
     * Test to prevent editing the end date of an active window to before todays date - no values are asserted as this
     * tests to ensure that a validation exception is thrown.
     */
    public function testIsActivePastEndDate()
    {
        $cmdData = [
            'id' => 1,
            'irhpPermitStock' => 1,
            'startDate' => '2017-12-01',
            'endDate' => '2017-12-30',
            'daysForPayment' => 14,
            'emissionsCategory' => PermitWindowEntity::EMISSIONS_CATEGORY_EURO6_REF
        ];

        $command = UpdateCmd::create($cmdData);

        $permitWindowEntity = m::mock(PermitWindowEntity::class);

        $permitWindowEntity->shouldReceive('getIrhpPermitStock->getIrhpPermitType->isEcmtAnnual')->once()->andReturn(true);

        $this->repoMap['IrhpPermitWindow']
            ->shouldReceive('fetchById')
            ->with($cmdData['id'])
            ->andReturn($permitWindowEntity);

        $permitWindowEntity
            ->shouldReceive('hasEnded')
            ->once()
            ->andReturn(false);

        $permitWindowEntity
            ->shouldReceive('isActive')
            ->andReturn(true);

        $permitWindowEntity
            ->shouldReceive('getStartDate')
            ->once()
            ->andReturn('2017-12-01');

        $this->sut->handleCommand($command);
    }

    /**
     * @expectedException \Dvsa\Olcs\Api\Domain\Exception\ValidationException
     * @expectedExceptionMessage The dates overlap with another window for this Permit stock
     *
     * Test for overlapping Windows - no values are asserted as this tests to ensure that a validation exception
     * is thrown.
     */
    public function testOverlappingWindows()
    {
        $cmdData = [
            'id' => 1,
            'irhpPermitStock' => 1,
            'startDate' => '2019-12-01',
            'endDate' => '2019-12-30',
            'daysForPayment' => 14,
            'emissionsCategory' => PermitWindowEntity::EMISSIONS_CATEGORY_EURO6_REF
        ];

        $command = UpdateCmd::create($cmdData);

        $permitWindowEntity = m::mock(PermitWindowEntity::class);

        $this->repoMap['IrhpPermitWindow']
            ->shouldReceive('fetchById')
            ->with('1')
            ->andReturn($permitWindowEntity);

        $permitWindowEntity->shouldReceive('hasEnded')->once()->andReturn(false);
        $permitWindowEntity->shouldReceive('isActive')->once()->andReturn(false);

        $permitWindowEntity->shouldReceive('getIrhpPermitStock->getIrhpPermitType->isEcmtAnnual')->once()->andReturn(true);

        $this->repoMap['IrhpPermitWindow']
            ->shouldReceive('findOverlappingWindowsByType')
            ->once()
            ->with(
                $cmdData['irhpPermitStock'],
                $cmdData['startDate'],
                $cmdData['endDate'],
                $cmdData['id']
            )
            ->andReturn([m::mock(PermitWindowEntity::class)]);

        $this->sut->handleCommand($command);
    }
}

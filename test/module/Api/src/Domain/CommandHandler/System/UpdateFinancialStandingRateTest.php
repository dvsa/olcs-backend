<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\System;

use Dvsa\Olcs\Api\Domain\CommandHandler\System\UpdateFinancialStandingRate as CommandHandler;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Api\Domain\Repository\FinancialStandingRate as Repo;
use Dvsa\Olcs\Api\Entity\System\FinancialStandingRate as Entity;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Transfer\Command\System\UpdateFinancialStandingRate as Command;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;

/**
 * UpdateFinancialStandingRate command handler test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class UpdateFinancialStandingRateTest extends CommandHandlerTestCase
{
    public function setUp(): void
    {
        $this->sut = new CommandHandler();
        $this->mockRepo('FinancialStandingRate', Repo::class);

        parent::setUp();
    }

    protected function initReferences()
    {
        $this->refData = [
            Licence::LICENCE_TYPE_STANDARD_NATIONAL,
            Licence::LICENCE_CATEGORY_GOODS_VEHICLE,
        ];

        parent::initReferences();
    }

    public function testHandleCommand()
    {
        $id = 69;
        $version = 2;

        $params = [
            'id' => $id,
            'version' => $version,
            'goodsOrPsv' => Licence::LICENCE_CATEGORY_GOODS_VEHICLE,
            'licenceType' => Licence::LICENCE_TYPE_STANDARD_NATIONAL,
            'firstVehicleRate' => '1000.01',
            'additionalVehicleRate' => '100.01',
            'effectiveFrom' => '2015-09-10',
        ];

        $command = Command::create($params);

        $mockRate = m::mock(Entity::class);

        $this->repoMap['FinancialStandingRate']
            ->shouldReceive('fetchUsingId')
            ->once()
            ->with($command, 1, $version)
            ->andReturn($mockRate)
            ->shouldReceive('fetchByCategoryTypeAndDate')
            ->once()
            ->andReturn([])
            ->shouldReceive('save')
            ->once()
            ->with($mockRate);

        $mockRate
            ->shouldReceive('setGoodsOrPsv')
            ->once()
            ->with($this->mapRefData(Licence::LICENCE_CATEGORY_GOODS_VEHICLE))
            ->andReturnSelf()
            ->shouldReceive('setLicenceType')
            ->once()
            ->with($this->mapRefData(Licence::LICENCE_TYPE_STANDARD_NATIONAL))
            ->andReturnSelf()
            ->shouldReceive('setFirstVehicleRate')
            ->once()
            ->with('1000.01')
            ->andReturnSelf()
            ->shouldReceive('setAdditionalVehicleRate')
            ->once()
            ->with('100.01')
            ->andReturnSelf()
            ->shouldReceive('setEffectiveFrom')
            ->once()
            ->with(
                m::on(
                    function ($arg) {
                        $this->assertInstanceOf(\DateTime::class, $arg);
                        $this->assertEquals('2015-09-10', $arg->format('Y-m-d'));
                        return true;
                    }
                )
            )
            ->andReturnSelf()
            ->shouldReceive('getId')
            ->andReturn($id);

        $response = $this->sut->handleCommand($command);

        $this->assertEquals(['financialStandingRate' => $id], $response->getIds());
        $this->assertEquals(['Financial Standing Rate updated'], $response->getMessages());
    }

    public function testHandleCommandDuplicateDetected()
    {
        $id = 69;
        $version = 2;

        $params = [
            'id' => $id,
            'version' => $version,
            'goodsOrPsv' => Licence::LICENCE_CATEGORY_GOODS_VEHICLE,
            'licenceType' => Licence::LICENCE_TYPE_STANDARD_NATIONAL,
            'firstVehicleRate' => '1000.01',
            'additionalVehicleRate' => '100.01',
            'effectiveFrom' => '2015-09-10',
        ];

        $command = Command::create($params);

        $mockRate = m::mock(Entity::class)
            ->shouldReceive('getId')
            ->andReturn($id)
            ->getMock();

        $mockExisting = m::mock(Entity::class)
            ->shouldReceive('getId')
            ->andReturn(99)
            ->getMock();

        $this->repoMap['FinancialStandingRate']
            ->shouldReceive('fetchUsingId')
            ->once()
            ->with($command, 1, $version)
            ->andReturn($mockRate)
            ->shouldReceive('fetchByCategoryTypeAndDate')
            ->once()
            ->with(Licence::LICENCE_CATEGORY_GOODS_VEHICLE, Licence::LICENCE_TYPE_STANDARD_NATIONAL, '2015-09-10')
            ->andReturn([$mockRate, $mockExisting])
            ->shouldReceive('save')
            ->never();

        $this->expectException(ValidationException::class);

        $this->sut->handleCommand($command);
    }
}

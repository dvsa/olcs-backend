<?php

namespace Dvsa\OlcsTest\Api\Domain\CommandHandler\System;

use Dvsa\Olcs\Api\Domain\CommandHandler\System\CreateFinancialStandingRate as CommandHandler;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Api\Domain\Repository\FinancialStandingRate as Repo;
use Dvsa\Olcs\Api\Entity\System\FinancialStandingRate as Entity;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Transfer\Command\System\CreateFinancialStandingRate as Command;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;

/**
 * CreateFinancialStandingRate command handler test
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
class CreateFinancialStandingRateTest extends CommandHandlerTestCase
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

        $this->references = [
            Entity::class => [
                99 => m::mock(Entity::class),
            ],
        ];

        parent::initReferences();
    }

    public function testHandleCommand()
    {
        $params = [
            'goodsOrPsv' => Licence::LICENCE_CATEGORY_GOODS_VEHICLE,
            'licenceType' => Licence::LICENCE_TYPE_STANDARD_NATIONAL,
            'firstVehicleRate' => '1000.01',
            'additionalVehicleRate' => '100.01',
            'effectiveFrom' => '2015-09-10',
        ];

        $command = Command::create($params);

        $newId = 69;
        $savedRate = null;
        $this->repoMap['FinancialStandingRate']
            ->shouldReceive('fetchByCategoryTypeAndDate')
            ->once()
            ->andReturn([])
            ->shouldReceive('save')
            ->once()
            ->andReturnUsing(
                function (Entity $rate) use (&$savedRate, $newId) {
                    $savedRate = $rate;
                    $savedRate->setId($newId);
                }
            );

        $response = $this->sut->handleCommand($command);

        $this->assertEquals(['financialStandingRate' => $newId], $response->getIds());
        $this->assertEquals(['Financial Standing Rate created'], $response->getMessages());

        $this->assertEquals($this->mapRefData(Licence::LICENCE_CATEGORY_GOODS_VEHICLE), $savedRate->getGoodsOrPsv());
        $this->assertEquals($this->mapRefData(Licence::LICENCE_TYPE_STANDARD_NATIONAL), $savedRate->getLicenceType());
        $this->assertEquals('1000.01', $savedRate->getFirstVehicleRate());
        $this->assertEquals('100.01', $savedRate->getAdditionalVehicleRate());
        $this->assertInstanceOf(\DateTime::class, $savedRate->getEffectiveFrom());
        $this->assertEquals('2015-09-10', $savedRate->getEffectiveFrom()->format('Y-m-d'));
    }

    public function testHandleCommandDuplicateDetected()
    {
        $params = [
            'goodsOrPsv' => Licence::LICENCE_CATEGORY_GOODS_VEHICLE,
            'licenceType' => Licence::LICENCE_TYPE_STANDARD_NATIONAL,
            'firstVehicleRate' => '1000.01',
            'additionalVehicleRate' => '100.01',
            'effectiveFrom' => '2015-09-10',
        ];

        $command = Command::create($params);

        $this->repoMap['FinancialStandingRate']
            ->shouldReceive('fetchByCategoryTypeAndDate')
            ->once()
            ->with(Licence::LICENCE_CATEGORY_GOODS_VEHICLE, Licence::LICENCE_TYPE_STANDARD_NATIONAL, '2015-09-10')
            ->andReturn(
                [
                    $this->mapReference(Entity::class, 99),
                ]
            )
            ->shouldReceive('save')
            ->never();

        $this->expectException(ValidationException::class);

        $this->sut->handleCommand($command);
    }
}

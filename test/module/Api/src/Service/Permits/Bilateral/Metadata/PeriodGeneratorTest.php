<?php

namespace Dvsa\OlcsTest\Api\Service\Permits\Bilateral\Metadata;

use Dvsa\Olcs\Api\Domain\Repository\IrhpPermitStock as IrhpPermitStockRepository;
use Dvsa\Olcs\Api\Entity\ContactDetails\Country;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitApplication;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitStock;
use Dvsa\Olcs\Api\Service\Permits\Bilateral\Metadata\FieldsGenerator;
use Dvsa\Olcs\Api\Service\Permits\Bilateral\Metadata\PeriodGenerator;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * PeriodGeneratorTest
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
class PeriodGeneratorTest extends MockeryTestCase
{
    /**
     * @dataProvider dpGenerate
     */
    public function testGenerate($irhpPermitApplication)
    {
        $stockId = 99;

        $periodNameKey = 'period.name.key';

        $fieldsResponse = [
            'fieldsResponseKey1' => 'fieldsResponseValue1',
            'fieldsResponseKey2' => 'fieldsResponseValue2'
        ];

        $irhpPermitStock = m::mock(IrhpPermitStock::class);
        $irhpPermitStock->shouldReceive('getPeriodNameKey')
            ->andReturn($periodNameKey);

        $irhpPermitStockRepo = m::mock(IrhpPermitStockRepository::class);
        $irhpPermitStockRepo->shouldReceive('fetchById')
            ->with($stockId)
            ->andReturn($irhpPermitStock);

        $fieldsGenerator = m::mock(FieldsGenerator::class);
        $fieldsGenerator->shouldReceive('generate')
            ->with($irhpPermitStock, $irhpPermitApplication)
            ->andReturn($fieldsResponse);

        $periodGenerator = new PeriodGenerator($irhpPermitStockRepo, $fieldsGenerator);

        $expected = [
            'id' => $stockId,
            'key' => $periodNameKey,
            'fields' => $fieldsResponse
        ];

        $this->assertEquals(
            $expected,
            $periodGenerator->generate($stockId, $irhpPermitApplication)
        );
    }

    public function dpGenerate()
    {
        return [
            [m::mock(IrhpPermitApplication::class)],
            [null]
        ];
    }
}

<?php

/**
 * Hearing Test
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Cases\Pi;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\QueryHandler\Cases\Pi\Hearing;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\PiHearing as PiHearingRepo;
use Dvsa\Olcs\Api\Domain\Repository\Sla as SlaRepo;
use Dvsa\Olcs\Transfer\Query\Cases\Pi\Hearing as Qry;
use Dvsa\Olcs\Api\Domain\QueryHandler\BundleSerializableInterface;
use Dvsa\Olcs\Api\Domain\QueryHandler\Result;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\Util\SlaCalculatorInterface;
use Dvsa\Olcs\Api\Entity\TrafficArea\TrafficArea as TrafficAreaEntity;
use Dvsa\Olcs\Api\Entity\Pi\Pi as PiEntity;

/**
 * Hearing Test
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class HearingTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new Hearing();
        $this->mockRepo('PiHearing', PiHearingRepo::class);
        $this->mockRepo('Sla', SlaRepo::class);

        $this->mockedSmServices = [
            SlaCalculatorInterface::class => m::mock(),
        ];
        parent::setUp();
    }

    /**
     * @dataProvider slaAppliesToProvider
     * @param $slaAppliesTo
     */
    public function testHandleQuery($slaAppliesTo)
    {
        $isTm = true;
        $slaCompareTo = 'agreedDate';
        $slaField = 'hearingDate';
        $agreedDate = '2015-12-25 00:00:00';
        $hearingDate = new \DateTime('2015-12-25 00:00:00');

        $query = Qry::create(['id' => 1]);

        $mockPi = m::mock(PiEntity::class);
        $mockPi->shouldReceive('getAgreedDate')->andReturn($agreedDate);
        $mockPi->shouldReceive('getCase->isTm')->andReturn($isTm);
        $mockPi->shouldReceive('getCase->isTm')->andReturn($isTm);

        $mockResult = m::mock(BundleSerializableInterface::class);
        $mockResult->shouldReceive('getPi')->andReturn($mockPi);

        $trafficArea = m::mock(TrafficAreaEntity::class);

        $sla = m::mock(SlaEntity::class);
        $sla->shouldReceive('getCompareTo')->andReturn($slaCompareTo);
        $sla->shouldReceive('getField')->andReturn($slaField);
        $sla->shouldReceive('appliesTo')->andReturn($slaAppliesTo);

        $slas = [
            0 => $sla
        ];

        $this->mockedSmServices[SlaCalculatorInterface::class]
            ->shouldReceive('applySla')
            ->andReturn($hearingDate);

        $this->repoMap['PiHearing']->shouldReceive('fetchUsingId')
            ->with($query)
            ->andReturn($mockResult)
            ->shouldReceive('getReference')
            ->with(TrafficAreaEntity::class, TrafficAreaEntity::SE_MET_TRAFFIC_AREA_CODE)
            ->andReturn($trafficArea)
            ->shouldReceive('getPi')
            ->andReturn($mockPi);

        $this->repoMap['Sla']->shouldReceive('fetchByCategories')
            ->with(['pi_hearing'], Query::HYDRATE_OBJECT)
            ->andReturn($slas);

        $result = $this->sut->handleQuery($query);
        $this->assertInstanceOf(Result::class, $result);
    }

    /**
     * Provides the possible responses to $sla->appliesTo()
     */
    public function slaAppliesToProvider()
    {
        return [
            [true],
            [false]
        ];
    }
}

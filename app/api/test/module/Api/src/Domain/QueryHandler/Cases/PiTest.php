<?php

/**
 * Pi Test
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Cases;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\QueryHandler\Result;
use Dvsa\Olcs\Api\Domain\QueryHandler\Cases\Pi;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\Cases as CasesRepo;
use Dvsa\Olcs\Api\Domain\Repository\Licence as LicenceRepo;
use Dvsa\Olcs\Api\Domain\Repository\Pi as PiRepo;
use Dvsa\Olcs\Api\Domain\Repository\Sla as SlaRepo;
use Dvsa\Olcs\Api\Domain\Repository\TrafficArea as TrafficAreaRepo;
use Dvsa\Olcs\Api\Entity\Pi\Pi as PiEntity;
use Dvsa\Olcs\Api\Entity\TrafficArea\TrafficArea as TrafficAreaEntity;
use Dvsa\Olcs\Api\Entity\System\Sla as SlaEntity;
use Dvsa\Olcs\Transfer\Query\Cases\Pi as Qry;
use Dvsa\Olcs\Api\Domain\Util\SlaCalculatorInterface;
use Mockery as m;

/**
 * Pi Test
 */
class PiTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new Pi();
        $this->mockRepo('Cases', CasesRepo::class);
        $this->mockRepo('Licence', LicenceRepo::class);
        $this->mockRepo('Pi', PiRepo::class);
        $this->mockRepo('Sla', SlaRepo::class);
        $this->mockRepo('TrafficArea', TrafficAreaRepo::class);

        $this->mockedSmServices = [
            SlaCalculatorInterface::class => m::mock(),
        ];

        parent::setUp();
    }

    /**
     * Tests an empty result is correctly dealt with
     */
    public function testHandleQueryEmptyResult()
    {
        $query = Qry::create(['id' => 1]);

        $this->repoMap['Pi']->shouldReceive('fetchUsingCase')
            ->with($query, Query::HYDRATE_OBJECT)
            ->andReturn(null);

        $this->assertEquals([], $this->sut->handleQuery($query));
    }

    /**
     * @dataProvider slaAppliesToProvider
     * @param $slaAppliesTo
     */
    public function testHandleQueryNotTm($slaAppliesTo)
    {
        $slaCompareTo = 'tcWrittenReasonDate';
        $slaField = 'writtenReasonLetterDate';
        $tcWrittenReasonDate = '2015-12-25 00:00:00';
        $tcWrittenReasonDateTime = new \DateTime('2015-12-25 00:00:00');

        $query = Qry::create(['id' => 1]);

        $trafficArea = m::mock(TrafficAreaEntity::class);

        $sla = m::mock(SlaEntity::class);
        $sla->shouldReceive('getCompareTo')->andReturn($slaCompareTo);
        $sla->shouldReceive('getField')->andReturn($slaField);
        $sla->shouldReceive('appliesTo')->andReturn($slaAppliesTo);

        $slas = [
            0 => $sla
        ];

        $pi = m::mock(PiEntity::class);
        $pi->shouldReceive('getCase->getLicence->getTrafficArea')->andReturn($trafficArea);
        $pi->shouldReceive('getTcWrittenReasonDate')->andReturn($tcWrittenReasonDate);
        $pi->shouldReceive('isTm')->andReturn(false);

        $this->mockedSmServices[SlaCalculatorInterface::class]
            ->shouldReceive('applySla')
            ->andReturn($tcWrittenReasonDateTime);

        $this->repoMap['Pi']->shouldReceive('fetchUsingCase')
            ->with($query, Query::HYDRATE_OBJECT)
            ->andReturn($pi);

        $this->repoMap['Sla']->shouldReceive('fetchByCategories')
        ->with(['pi', 'pi_hearing'], Query::HYDRATE_OBJECT)
        ->andReturn($slas);

        $this->assertInstanceOf(Result::class, $this->sut->handleQuery($query));
    }

    /**
     * @dataProvider slaAppliesToProvider
     * @param $slaAppliesTo
     */
    public function testHandleQueryTm($slaAppliesTo)
    {
        $slaCompareTo = 'tcWrittenReasonDate';
        $slaField = 'writtenReasonLetterDate';
        $tcWrittenReasonDate = '2015-12-25 00:00:00';
        $tcWrittenReasonDateTime = new \DateTime('2015-12-25 00:00:00');

        $query = Qry::create(['id' => 1]);

        $trafficArea = m::mock(TrafficAreaEntity::class);

        $sla = m::mock(SlaEntity::class);
        $sla->shouldReceive('getCompareTo')->andReturn($slaCompareTo);
        $sla->shouldReceive('getField')->andReturn($slaField);
        $sla->shouldReceive('appliesTo')->andReturn($slaAppliesTo);

        $slas = [
            0 => $sla
        ];

        $pi = m::mock(PiEntity::class);
        $pi->shouldReceive('getTcWrittenReasonDate')->andReturn($tcWrittenReasonDate);
        $pi->shouldReceive('isTm')->andReturn(true);

        $this->mockedSmServices[SlaCalculatorInterface::class]
            ->shouldReceive('applySla')
            ->andReturn($tcWrittenReasonDateTime);

        $this->repoMap['Pi']->shouldReceive('fetchUsingCase')
            ->with($query, Query::HYDRATE_OBJECT)
            ->andReturn($pi)
            ->shouldReceive('getReference')
            ->with(TrafficAreaEntity::class, TrafficAreaEntity::SE_MET_TRAFFIC_AREA_CODE)
            ->andReturn($trafficArea);

        $this->repoMap['Sla']->shouldReceive('fetchByCategories')
            ->with(['pi', 'pi_hearing'], Query::HYDRATE_OBJECT)
            ->andReturn($slas);

        $this->assertInstanceOf(Result::class, $this->sut->handleQuery($query));
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

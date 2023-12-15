<?php

/**
 * Hearing Test
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Cases\Pi;

use Dvsa\Olcs\Api\Domain\QueryHandler\Cases\Pi\Hearing;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\PiHearing as PiHearingRepo;
use Dvsa\Olcs\Transfer\Query\Cases\Pi\Hearing as Qry;
use Dvsa\Olcs\Api\Domain\QueryHandler\BundleSerializableInterface;
use Dvsa\Olcs\Api\Domain\QueryHandler\Result;
use Mockery as m;
use Dvsa\Olcs\Api\Entity\Pi\Pi as PiEntity;

/**
 * Hearing Test
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
class HearingTest extends QueryHandlerTestCase
{
    public function setUp(): void
    {
        $this->sut = new Hearing();
        $this->mockRepo('PiHearing', PiHearingRepo::class);
        parent::setUp();
    }

    public function testHandleQuery()
    {
        $isTm = true;
        $agreedDate = '2015-12-25 00:00:00';
        $slaTargetDate = new \DateTime('2015-12-25 00:00:00');

        $query = Qry::create(['id' => 1]);

        $mockPi = m::mock(PiEntity::class);
        $mockPi->shouldReceive('getAgreedDate')->andReturn($agreedDate);
        $mockPi->shouldReceive('getCase->isTm')->andReturn($isTm);
        $mockPi->shouldReceive('flattenSlaTargetDates')->once()->andReturn(['slaTarget' => $slaTargetDate]);

        $mockResult = m::mock(BundleSerializableInterface::class);
        $mockResult->shouldReceive('getPi')->andReturn($mockPi);

        $this->repoMap['PiHearing']->shouldReceive('fetchUsingId')
            ->with($query)
            ->andReturn($mockResult);

        $result = $this->sut->handleQuery($query);
        $this->assertInstanceOf(Result::class, $result);
    }
}

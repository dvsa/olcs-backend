<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Cases;

use Mockery as m;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\QueryHandler\Cases\CasesWithOppositionDates as QueryHandler;
use Dvsa\Olcs\Transfer\Query\Cases\CasesWithOppositionDates as Qry;

/**
 * CasesWithOppositionDatesTest
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class CasesWithOppositionDatesTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new QueryHandler();
        $this->mockRepo('Cases', \Dvsa\Olcs\Api\Domain\Repository\Cases::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Qry::create(['id' => 1]);

        $mockApplication = m::mock(\Dvsa\Olcs\Api\Entity\Application\Application::class);
        $mockApplication->shouldReceive('getOutOfOppositionDate')->with()->once()->andReturn('STRING');
        $mockApplication->shouldReceive('getOutOfRepresentationDate')->with()->once()->andReturn('STRING');

        $mockCase = m::mock(\Dvsa\Olcs\Api\Entity\Cases\Cases::class);
        $mockCase->shouldReceive('getApplication')->with()->andReturn($mockApplication);
        $mockCase->shouldReceive('serialize')->andReturn(['SERIALIZED']);

        $this->repoMap['Cases']->shouldReceive('getReference')
            ->with(\Dvsa\Olcs\Api\Entity\Publication\PublicationSection::class, 1)->andReturn(1);
        $this->repoMap['Cases']->shouldReceive('getReference')
            ->with(\Dvsa\Olcs\Api\Entity\Publication\PublicationSection::class, 3)->andReturn(1);

        $this->repoMap['Cases']->shouldReceive('fetchUsingId')->with($query)->andReturn($mockCase);

        $result = $this->sut->handleQuery($query);

        $this->assertSame(['SERIALIZED', 'oooDate' => '', 'oorDate' => ''], $result->serialize());
    }

    public function testHandleQueryWithDates()
    {
        $query = Qry::create(['id' => 1]);

        $mockApplication = m::mock(\Dvsa\Olcs\Api\Entity\Application\Application::class);
        $mockApplication->shouldReceive('getOutOfOppositionDate')->with()->once()
            ->andReturn(new \DateTime('1996-07-27'));
        $mockApplication->shouldReceive('getOutOfRepresentationDate')->with()->once()
            ->andReturn(new \DateTime('2002-10-02'));

        $mockCase = m::mock(\Dvsa\Olcs\Api\Entity\Cases\Cases::class);
        $mockCase->shouldReceive('getApplication')->with()->andReturn($mockApplication);
        $mockCase->shouldReceive('serialize')->andReturn(['SERIALIZED']);

        $this->repoMap['Cases']->shouldReceive('getReference')
            ->with(\Dvsa\Olcs\Api\Entity\Publication\PublicationSection::class, 1)->andReturn(1);
        $this->repoMap['Cases']->shouldReceive('getReference')
            ->with(\Dvsa\Olcs\Api\Entity\Publication\PublicationSection::class, 3)->andReturn(1);
        $this->repoMap['Cases']->shouldReceive('fetchUsingId')->with($query)->andReturn($mockCase);

        $result = $this->sut->handleQuery($query);

        $this->assertSame(
            [
                'SERIALIZED',
                'oooDate' => '1996-07-27T00:00:00+0000',
                'oorDate' => '2002-10-02T00:00:00+0000',
            ],
            $result->serialize()
        );
    }
}

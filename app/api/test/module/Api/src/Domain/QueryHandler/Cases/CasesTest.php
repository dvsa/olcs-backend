<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Cases;

use Dvsa\Olcs\Api\Domain\QueryHandler\Cases\Cases;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\Cases as CasesRepo;
use Dvsa\Olcs\Api\Domain\Repository\Note as NoteRepo;
use Dvsa\Olcs\Transfer\Query\Cases\Cases as Qry;
use Mockery as m;

/**
 * Cases test
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
class CasesTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new Cases();
        $this->mockRepo('Cases', CasesRepo::class);
        $this->mockRepo('Note', NoteRepo::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Qry::create(['id' => 24]);
        $noteType = 'note type';
        $licenceId = 77;
        $latestNote = 'test note';

        $mockLicence = m::mock(\Dvsa\Olcs\Api\Entity\Licence\Licence::class);
        $mockLicence->shouldReceive('getId')->andReturn($licenceId);

        $mockCase = m::mock(\Dvsa\Olcs\Api\Entity\Cases\Cases::class);
        $mockCase->shouldReceive('serialize')->andReturn(['SERIALIZED']);
        $mockCase->shouldReceive('getNoteType')->andReturn($noteType);
        $mockCase->shouldReceive('getLicence')->andReturn($mockLicence);

        $this->repoMap['Cases']->shouldReceive('fetchUsingId')->with($query)->andReturn($mockCase);
        $this->repoMap['Note']->shouldReceive('fetchForOverview')
            ->with($licenceId, null, $noteType)->andReturn($latestNote);

        $result = $this->sut->handleQuery($query);

        $this->assertSame(
            [
                'SERIALIZED',
                'latestNote' => 'test note'
            ],
            $result->serialize()
        );
        $this->assertInstanceOf('Dvsa\Olcs\Api\Domain\QueryHandler\Result', $result);
    }
}

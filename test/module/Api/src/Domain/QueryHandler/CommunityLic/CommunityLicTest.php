<?php

/**
 * CommunityLic Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\CommunityLic;

use Dvsa\Olcs\Api\Domain\QueryHandler\CommunityLic\CommunityLic as CommunityLicQueryHandler;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Transfer\Query\CommunityLic\CommunityLic as Qry;
use Dvsa\Olcs\Api\Domain\Repository\CommunityLic as CommunityLicRepo;
use Dvsa\Olcs\Api\Domain\Repository\Licence as LicenceRepo;
use Doctrine\ORM\Query;
use Mockery as m;

/**
 * CommunityLic Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class CommunityLicTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new CommunityLicQueryHandler();
        $this->mockRepo('CommunityLic', CommunityLicRepo::class);
        $this->mockRepo('Licence', LicenceRepo::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $licenceId = 1;
        $query = Qry::create(['licence' => $licenceId]);

        $mockLicence = m::mock()
            ->shouldReceive('getTotCommunityLicences')
            ->andReturn(2)
            ->once()
            ->getMock();

        $this->repoMap['Licence']->shouldReceive('fetchById')
            ->with($licenceId)
            ->andReturn($mockLicence)
            ->once()
            ->getMock();

        $this->repoMap['CommunityLic']->shouldReceive('fetchOfficeCopy')
            ->with($licenceId)
            ->andReturn('officeCopy')
            ->once()
            ->shouldReceive('fetchList')
            ->with($query)
            ->andReturn('result')
            ->once()
            ->shouldReceive('fetchCount')
            ->with($query)
            ->andReturn(1)
            ->shouldReceive('fetchCount')
            ->with(m::type(Qry::class))
            ->andReturn(2)
            ->once()
            ->getMock();

        $result = $this->sut->handleQuery($query);

        $expected = [
            'result' => 'result',
            'count' =>  1,
            'count-unfiltered' => 2,
            'totCommunityLicences' => 2,
            'officeCopy' => 'officeCopy'
        ];

        $this->assertEquals($result, $expected);
    }
}

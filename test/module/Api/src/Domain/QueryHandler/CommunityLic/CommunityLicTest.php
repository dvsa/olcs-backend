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

        $mockOfficeCopy = m::mock(\Dvsa\Olcs\Api\Domain\QueryHandler\BundleSerializableInterface::class);

        $this->repoMap['CommunityLic']->shouldReceive('fetchOfficeCopy')
            ->with($licenceId)
            ->andReturn($mockOfficeCopy)
            ->once()
            ->shouldReceive('fetchList')
            ->with($query)
            ->andReturn('result')
            ->once()
            ->shouldReceive('fetchCount')
            ->with($query)
            ->andReturn(15)
            ->shouldReceive('hasRows')
            ->with(m::type(Qry::class))
            ->andReturn(1)
            ->once()
            ->getMock();

        $result = $this->sut->handleQuery($query);

        $expected = [
            'result' => 'result',
            'count' =>  15,
            'count-unfiltered' => 1,
            'totCommunityLicences' => 2
        ];
        $this->assertInstanceOf('Dvsa\Olcs\Api\Domain\QueryHandler\Result', $result['officeCopy']);

        unset($result['officeCopy']);
        $this->assertEquals($result, $expected);
    }
}

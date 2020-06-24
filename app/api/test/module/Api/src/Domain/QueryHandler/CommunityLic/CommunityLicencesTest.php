<?php

/**
 * CommunityLic Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\CommunityLic;

use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\Olcs\Api\Domain\QueryHandler\CommunityLic\CommunityLicences as CommunityLicencesQueryHandler;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Transfer\Query\CommunityLic\CommunityLicences as Qry;
use Dvsa\Olcs\Api\Domain\Repository\CommunityLic as CommunityLicRepo;
use Dvsa\Olcs\Api\Domain\Repository\Licence as LicenceRepo;
use Mockery as m;
use Doctrine\ORM\Query;

/**
 * CommunityLic Test
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
class CommunityLicencesTest extends QueryHandlerTestCase
{
    public function setUp(): void
    {
        $this->sut = new CommunityLicencesQueryHandler();
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

        $mockOfficeCopy = m::mock(\Dvsa\Olcs\Api\Domain\QueryHandler\BundleSerializableInterface::class)->shouldReceive('serialize')->andReturn(['item'])->getMock();
        $mockComLic = m::mock(\Dvsa\Olcs\Api\Domain\QueryHandler\BundleSerializableInterface::class)
            ->shouldReceive('serialize')
            ->once()
            ->andReturn('result')
            ->getMock();
        $comLics = new ArrayCollection();
        $comLics->add($mockComLic);

        $this->repoMap['CommunityLic']->shouldReceive('fetchOfficeCopy')
            ->with($licenceId)
            ->andReturn($mockOfficeCopy)
            ->once()
            ->shouldReceive('fetchList')
            ->with($query, Query::HYDRATE_OBJECT)
            ->andReturn($comLics)
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
            'result' => ['result'],
            'count' =>  15,
            'count-unfiltered' => 1,
            'totCommunityLicences' => 2,
            'officeCopy' => ['item']
        ];

        $this->assertEquals($result, $expected);
    }
}

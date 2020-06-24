<?php

/**
 * LicenceDecisionsTest.php
 *
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Licence;

use Dvsa\Olcs\Api\Domain\QueryHandler\Licence\LicenceDecisions;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\Licence as LicenceRepo;
use Dvsa\Olcs\Transfer\Query\Licence\LicenceDecisions as Qry;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Doctrine\Common\Collections\ArrayCollection;
use Mockery as m;

/**
 * Licence Decisions Test
 *
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 */
class LicenceDecisionsTest extends QueryHandlerTestCase
{
    public function setUp(): void
    {
        $this->sut = new LicenceDecisions();
        $this->mockRepo('Licence', LicenceRepo::class);
        $this->mockRepo('BusRegSearchView', \Dvsa\Olcs\Api\Domain\Repository\BusRegSearchView::class);

        parent::setUp();
    }

    public function testHandleQueryDecisionsTrue()
    {
        $query = Qry::create(['licence' => 1]);

        $licence = m::mock(LicenceEntity::class)->makePartial();
        $licence->shouldReceive('getActiveCommunityLicences')->andReturn(new ArrayCollection(['one']));
        $licence->shouldReceive('getActiveVariations')->andReturn(new ArrayCollection(['one']));
        $licence->shouldReceive('serialize')->andReturn(['foo' => 'bar']);

        $this->repoMap['Licence']->shouldReceive('fetchUsingId')
            ->with($query)
            ->andReturn($licence);

        $this->repoMap['BusRegSearchView']->shouldReceive('fetchActiveByLicence')->with($licence)->once()
            ->andReturn(new ArrayCollection(['one']));

        $result = $this->sut->handleQuery($query);
        $this->assertEquals(
            [
                'foo' => 'bar',
                'suitableForDecisions' => [
                    'activeComLics' => true,
                    'activeBusRoutes' => true,
                    'activeVariations' => true,
                ]
            ],
            $result->serialize()
        );
    }

    public function testHandleQueryDecisionsFalse()
    {
        $query = Qry::create(['licence' => 1]);

        $licence = m::mock(LicenceEntity::class)->makePartial();
        $licence->shouldReceive('getActiveCommunityLicences')->andReturn(new ArrayCollection([]));
        $licence->shouldReceive('getActiveVariations')->andReturn(new ArrayCollection([]));
        $licence->shouldReceive('serialize')->andReturn(['foo' => 'bar']);

        $this->repoMap['Licence']->shouldReceive('fetchUsingId')
            ->with($query)
            ->andReturn($licence);

        $this->repoMap['BusRegSearchView']->shouldReceive('fetchActiveByLicence')->with($licence)->once()
            ->andReturn(new ArrayCollection([]));

        $result = $this->sut->handleQuery($query);
        $this->assertEquals(
            [
                'foo' => 'bar',
                'suitableForDecisions' => true
            ],
            $result->serialize()
        );
    }
}

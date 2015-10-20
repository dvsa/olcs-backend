<?php

/**
 * Operating Centres Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Licence;

use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\TrafficArea\TrafficArea;
use Dvsa\Olcs\Api\Entity\TrafficArea\TrafficAreaEnforcementArea;
use Dvsa\Olcs\Api\Entity\User\Permission;
use Dvsa\Olcs\Api\Entity\EnforcementArea\EnforcementArea;
use Mockery as m;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\QueryHandler\Licence\OperatingCentres as QueryHandler;
use Dvsa\Olcs\Transfer\Query\Licence\OperatingCentres as Qry;
use ZfcRbac\Service\AuthorizationService;
use Dvsa\Olcs\Api\Domain\Repository;

/**
 * Operating Centres Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class OperatingCentresTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new QueryHandler();
        $this->mockRepo('Licence', Repository\Licence::class);
        $this->mockRepo('LicenceOperatingCentre', Repository\LicenceOperatingCentre::class);
        $this->mockRepo('TrafficArea', Repository\TrafficArea::class);
        $this->mockRepo('Document', Repository\Document::class);

        $this->mockedSmServices[AuthorizationService::class] = m::mock(AuthorizationService::class);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Qry::create(['id' => 111]);

        $bundle = [
            'trafficArea',
            'enforcementArea'
        ];

        /** @var Licence $licence */
        $licence = m::mock(Licence::class)->makePartial();
        $licence->shouldReceive('serialize')->with($bundle)->andReturn(['foo' => 'bar']);
        $licence->shouldReceive('isPsv')->andReturn(false);
        $licence->shouldReceive('canHaveCommunityLicences')->andReturn(true);
        $licence->setId(111);

        $this->repoMap['Licence']->shouldReceive('fetchUsingId')
            ->with($query)
            ->andReturn($licence);

        $this->repoMap['LicenceOperatingCentre']->shouldReceive('fetchByLicenceIdForOperatingCentres')
            ->with(111)
            ->andReturn(['a', 'b']);

        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('isGranted')
            ->with(Permission::SELFSERVE_USER, null)
            ->andReturn(true);

        $this->repoMap['TrafficArea']->shouldReceive('getValueOptions')
            ->andReturn(['foo' => 'bar']);

        $documents = [];

        $this->repoMap['Document']->shouldReceive('fetchUnlinkedOcDocumentsForEntity')
            ->with($licence)
            ->andReturn($documents);

        $result = $this->sut->handleQuery($query);

        $expected = [
            'foo' => 'bar',
            'requiresVariation' => true,
            'operatingCentres' => ['a', 'b'],
            'isPsv' => false,
            'canHaveCommunityLicences' => true,
            'canHaveSchedule41' => false,
            'possibleEnforcementAreas' => [],
            'possibleTrafficAreas' => ['foo' => 'bar'],
            'canAddAnother' => true,
            'documents' => []
        ];

        $this->assertEquals($expected, $result->serialize());
    }

    public function testHandleQueryWithTa()
    {
        $query = Qry::create(['id' => 111]);

        $bundle = [
            'trafficArea',
            'enforcementArea'
        ];

        /** @var EnforcementArea $ea */
        $ea = m::mock(EnforcementArea::class)->makePartial();
        $ea->setId(33);
        $ea->setName('EA');

        /** @var TrafficAreaEnforcementArea $taea */
        $taea = m::mock(TrafficAreaEnforcementArea::class)->makePartial();
        $taea->setEnforcementArea($ea);

        $taeas = [
            $taea
        ];

        /** @var TrafficArea $ta */
        $ta = m::mock(TrafficArea::class)->makePartial();
        $ta->setTrafficAreaEnforcementAreas($taeas);

        /** @var Licence $licence */
        $licence = m::mock(Licence::class)->makePartial();
        $licence->shouldReceive('serialize')->with($bundle)->andReturn(['foo' => 'bar']);
        $licence->shouldReceive('isPsv')->andReturn(false);
        $licence->shouldReceive('canHaveCommunityLicences')->andReturn(true);
        $licence->setId(111);
        $licence->setTrafficArea($ta);

        $this->repoMap['Licence']->shouldReceive('fetchUsingId')
            ->with($query)
            ->andReturn($licence);

        $this->repoMap['LicenceOperatingCentre']->shouldReceive('fetchByLicenceIdForOperatingCentres')
            ->with(111)
            ->andReturn(['a', 'b']);

        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('isGranted')
            ->with(Permission::SELFSERVE_USER, null)
            ->andReturn(true);

        $this->repoMap['TrafficArea']->shouldReceive('getValueOptions')
            ->andReturn(['foo' => 'bar']);

        $documents = [];

        $this->repoMap['Document']->shouldReceive('fetchUnlinkedOcDocumentsForEntity')
            ->with($licence)
            ->andReturn($documents);

        $result = $this->sut->handleQuery($query);

        $expected = [
            'foo' => 'bar',
            'requiresVariation' => true,
            'operatingCentres' => ['a', 'b'],
            'isPsv' => false,
            'canHaveCommunityLicences' => true,
            'canHaveSchedule41' => false,
            'possibleEnforcementAreas' => [33 => 'EA'],
            'possibleTrafficAreas' => [],
            'canAddAnother' => true,
            'documents' => []
        ];

        $this->assertEquals($expected, $result->serialize());
    }
}

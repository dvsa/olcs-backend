<?php

/**
 * Operating Centres Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Application;

use Dvsa\Olcs\Api\Domain\Service\VariationOperatingCentreHelper;
use Dvsa\Olcs\Api\Entity\Application\Application;
use Dvsa\Olcs\Api\Entity\EnforcementArea\EnforcementArea;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Dvsa\Olcs\Api\Entity\TrafficArea\TrafficArea;
use Dvsa\Olcs\Api\Entity\TrafficArea\TrafficAreaEnforcementArea;
use Dvsa\Olcs\Api\Entity\User\Permission;
use Mockery as m;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\QueryHandler\Application\OperatingCentres as QueryHandler;
use Dvsa\Olcs\Transfer\Query\Application\OperatingCentres as Qry;
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
        $this->mockRepo('Application', Repository\Application::class);
        $this->mockRepo('ApplicationOperatingCentre', Repository\ApplicationOperatingCentre::class);
        $this->mockRepo('TrafficArea', Repository\TrafficArea::class);
        $this->mockRepo('Document', Repository\Document::class);

        $this->mockedSmServices['VariationOperatingCentreHelper'] = m::mock(VariationOperatingCentreHelper::class);
        $this->mockedSmServices[AuthorizationService::class] = m::mock(AuthorizationService::class);

        parent::setUp();
    }

    public function testHandleQueryNewApp()
    {
        $query = Qry::create(['id' => 111]);

        $bundle = [
            'licence' => [
                'trafficArea',
                'enforcementArea'
            ]
        ];

        /** @var Licence $licence */
        $licence = m::mock(Licence::class)->makePartial();

        /** @var Application $application */
        $application = m::mock(Application::class)->makePartial();
        $application->shouldReceive('serialize')->with($bundle)->andReturn(['foo' => 'bar']);
        $application->shouldReceive('isPsv')->andReturn(false);
        $application->shouldReceive('canHaveCommunityLicences')->andReturn(true);
        $application->setIsVariation(false);
        $application->setId(111);
        $application->setTotCommunityLicences(12);
        $application->setLicence($licence);
        $application->setNiFlag('N');

        $this->repoMap['Application']->shouldReceive('fetchUsingId')
            ->with($query)
            ->andReturn($application);

        $this->repoMap['ApplicationOperatingCentre']->shouldReceive('fetchByApplicationIdForOperatingCentres')
            ->with(111, $query)
            ->andReturn(['a', 'b']);

        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('isGranted')
            ->with(Permission::SELFSERVE_USER, null)
            ->andReturn(true);

        $this->repoMap['TrafficArea']->shouldReceive('getValueOptions')
            ->andReturn(['foo' => 'bar']);

        $documents = [];

        $this->repoMap['Document']->shouldReceive('fetchUnlinkedOcDocumentsForEntity')
            ->with($application)
            ->andReturn($documents);

        $result = $this->sut->handleQuery($query);

        $expected = [
            'foo' => 'bar',
            'requiresVariation' => false,
            'operatingCentres' => ['a', 'b'],
            'totCommunityLicences' => 12,
            'isPsv' => false,
            'canHaveCommunityLicences' => true,
            'canHaveSchedule41' => false,
            'possibleEnforcementAreas' => [],
            'possibleTrafficAreas' => ['foo' => 'bar'],
            'canAddAnother' => false,
            'documents' => []
        ];

        $this->assertEquals($expected, $result->serialize());
    }

    public function testHandleQueryVariationWithTaPsv()
    {
        $query = Qry::create(['id' => 111]);

        $bundle = [
            'licence' => [
                'trafficArea',
                'enforcementArea'
            ]
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
        $licence->setTotCommunityLicences(12);
        $licence->setTrafficArea($ta);

        /** @var Application $application */
        $application = m::mock(Application::class)->makePartial();
        $application->shouldReceive('serialize')->with($bundle)->andReturn(['foo' => 'bar']);
        $application->shouldReceive('isPsv')->andReturn(true);
        $application->shouldReceive('canHaveCommunityLicences')->andReturn(true);
        $application->setIsVariation(true);
        $application->setId(111);
        $application->setLicence($licence);
        $application->setNiFlag('N');

        $this->repoMap['Application']->shouldReceive('fetchUsingId')
            ->with($query)
            ->andReturn($application);

        $this->mockedSmServices['VariationOperatingCentreHelper']
            ->shouldReceive('getListDataForApplication')
            ->with($application, $query)
            ->andReturn(['a', 'b']);

        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('isGranted')
            ->with(Permission::SELFSERVE_USER, null)
            ->andReturn(false);

        $this->repoMap['TrafficArea']->shouldReceive('getValueOptions')
            ->andReturn(['foo' => 'bar']);

        $documents = [];

        $this->repoMap['Document']->shouldReceive('fetchUnlinkedOcDocumentsForEntity')
            ->with($application)
            ->andReturn($documents);

        $result = $this->sut->handleQuery($query);

        $expected = [
            'foo' => 'bar',
            'requiresVariation' => false,
            'operatingCentres' => ['a', 'b'],
            'totCommunityLicences' => 12,
            'isPsv' => true,
            'canHaveCommunityLicences' => true,
            'canHaveSchedule41' => false,
            'possibleEnforcementAreas' => [33 => 'EA'],
            'possibleTrafficAreas' => ['foo' => 'bar'],
            'canAddAnother' => true,
            'documents' => []
        ];

        $this->assertEquals($expected, $result->serialize());
    }

    public function testHandleQueryVariationWithTaGoods()
    {
        $query = Qry::create(['id' => 111]);

        $bundle = [
            'licence' => [
                'trafficArea',
                'enforcementArea'
            ]
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
        $licence->setTotCommunityLicences(12);
        $licence->setTrafficArea($ta);

        $status = m::mock(RefData::class)->makePartial();
        $status->setId(Application::APPLICATION_STATUS_GRANTED);

        /** @var Application $application */
        $application = m::mock(Application::class)->makePartial();
        $application->shouldReceive('serialize')->with($bundle)->andReturn(['foo' => 'bar']);
        $application->shouldReceive('isPsv')->andReturn(false);
        $application->shouldReceive('canHaveCommunityLicences')->andReturn(true);
        $application->setIsVariation(true);
        $application->setId(111);
        $application->setLicence($licence);
        $application->setNiFlag('N');
        $application->setStatus($status);

        $this->repoMap['Application']->shouldReceive('fetchUsingId')
            ->with($query)
            ->andReturn($application);

        $this->mockedSmServices['VariationOperatingCentreHelper']
            ->shouldReceive('getListDataForApplication')
            ->with($application, $query)
            ->andReturn(['a', 'b']);

        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('isGranted')
            ->with(Permission::SELFSERVE_USER, null)
            ->andReturn(false);

        $this->repoMap['TrafficArea']->shouldReceive('getValueOptions')
            ->andReturn(['foo' => 'bar']);

        $documents = [];

        $this->repoMap['Document']->shouldReceive('fetchUnlinkedOcDocumentsForEntity')
            ->with($application)
            ->andReturn($documents);

        $result = $this->sut->handleQuery($query);

        $expected = [
            'foo' => 'bar',
            'requiresVariation' => false,
            'operatingCentres' => ['a', 'b'],
            'totCommunityLicences' => 12,
            'isPsv' => false,
            'canHaveCommunityLicences' => true,
            'canHaveSchedule41' => false,
            'possibleEnforcementAreas' => [33 => 'EA'],
            'possibleTrafficAreas' => ['foo' => 'bar'],
            'canAddAnother' => true,
            'documents' => []
        ];

        $this->assertEquals($expected, $result->serialize());
    }

    public function testHandleQueryVariationWithTaGoodsUnderConsideration()
    {
        $query = Qry::create(['id' => 111]);

        $bundle = [
            'licence' => [
                'trafficArea',
                'enforcementArea'
            ]
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
        $licence->setTotCommunityLicences(12);
        $licence->setTrafficArea($ta);

        $status = m::mock(RefData::class)->makePartial();
        $status->setId(Application::APPLICATION_STATUS_UNDER_CONSIDERATION);

        /** @var Application $application */
        $application = m::mock(Application::class)->makePartial();
        $application->shouldReceive('serialize')->with($bundle)->andReturn(['foo' => 'bar']);
        $application->shouldReceive('isPsv')->andReturn(false);
        $application->shouldReceive('canHaveCommunityLicences')->andReturn(true);
        $application->shouldReceive('getActiveS4s')->andReturn(['foo']);
        $application->setIsVariation(true);
        $application->setId(111);
        $application->setLicence($licence);
        $application->setNiFlag('N');
        $application->setStatus($status);

        $this->repoMap['Application']->shouldReceive('fetchUsingId')
            ->with($query)
            ->andReturn($application);

        $this->mockedSmServices['VariationOperatingCentreHelper']
            ->shouldReceive('getListDataForApplication')
            ->with($application, $query)
            ->andReturn(['a', 'b']);

        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('isGranted')
            ->with(Permission::SELFSERVE_USER, null)
            ->andReturn(false);

        $this->repoMap['TrafficArea']->shouldReceive('getValueOptions')
            ->andReturn(['foo' => 'bar']);

        $documents = [];

        $this->repoMap['Document']->shouldReceive('fetchUnlinkedOcDocumentsForEntity')
            ->with($application)
            ->andReturn($documents);

        $result = $this->sut->handleQuery($query);

        $expected = [
            'foo' => 'bar',
            'requiresVariation' => false,
            'operatingCentres' => ['a', 'b'],
            'totCommunityLicences' => 12,
            'isPsv' => false,
            'canHaveCommunityLicences' => true,
            'canHaveSchedule41' => false,
            'possibleEnforcementAreas' => [33 => 'EA'],
            'possibleTrafficAreas' => ['foo' => 'bar'],
            'canAddAnother' => true,
            'documents' => []
        ];

        $this->assertEquals($expected, $result->serialize());
    }

    public function testHandleQueryVariationWithTaGoodsUnderConsiderationWithoutActiveS4()
    {
        $query = Qry::create(['id' => 111]);

        $bundle = [
            'licence' => [
                'trafficArea',
                'enforcementArea'
            ]
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
        $licence->setTotCommunityLicences(12);
        $licence->setTrafficArea($ta);

        $status = m::mock(RefData::class)->makePartial();
        $status->setId(Application::APPLICATION_STATUS_UNDER_CONSIDERATION);

        /** @var Application $application */
        $application = m::mock(Application::class)->makePartial();
        $application->shouldReceive('serialize')->with($bundle)->andReturn(['foo' => 'bar']);
        $application->shouldReceive('isPsv')->andReturn(false);
        $application->shouldReceive('canHaveCommunityLicences')->andReturn(true);
        $application->shouldReceive('getActiveS4s')->andReturn([]);
        $application->setIsVariation(true);
        $application->setId(111);
        $application->setLicence($licence);
        $application->setNiFlag('N');
        $application->setStatus($status);

        $this->repoMap['Application']->shouldReceive('fetchUsingId')
            ->with($query)
            ->andReturn($application);

        $this->mockedSmServices['VariationOperatingCentreHelper']
            ->shouldReceive('getListDataForApplication')
            ->with($application, $query)
            ->andReturn(['a', 'b']);

        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('isGranted')
            ->with(Permission::SELFSERVE_USER, null)
            ->andReturn(false);

        $this->repoMap['TrafficArea']->shouldReceive('getValueOptions')
            ->andReturn(['foo' => 'bar']);

        $documents = [];

        $this->repoMap['Document']->shouldReceive('fetchUnlinkedOcDocumentsForEntity')
            ->with($application)
            ->andReturn($documents);

        $result = $this->sut->handleQuery($query);

        $expected = [
            'foo' => 'bar',
            'requiresVariation' => false,
            'operatingCentres' => ['a', 'b'],
            'totCommunityLicences' => 12,
            'isPsv' => false,
            'canHaveCommunityLicences' => true,
            'canHaveSchedule41' => true,
            'possibleEnforcementAreas' => [33 => 'EA'],
            'possibleTrafficAreas' => ['foo' => 'bar'],
            'canAddAnother' => true,
            'documents' => []
        ];

        $this->assertEquals($expected, $result->serialize());
    }
}

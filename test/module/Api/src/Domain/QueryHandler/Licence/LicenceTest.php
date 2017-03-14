<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Licence;

use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\QueryHandler\Licence\Licence;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\Licence as LicenceRepo;
use Dvsa\Olcs\Transfer\Query\Licence\Licence as Qry;
use ZfcRbac\Service\AuthorizationService;
use Dvsa\OlcsTest\Api\Entity\User as UserEntity;

/**
 * Licence Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class LicenceTest extends QueryHandlerTestCase
{
    public function setUp()
    {
        $this->sut = new Licence();
        $this->mockRepo('Licence', LicenceRepo::class);
        $this->mockRepo('ContinuationDetail', \Dvsa\Olcs\Api\Domain\Repository\ContinuationDetail::class);
        $this->mockRepo('Note', \Dvsa\Olcs\Api\Domain\Repository\Note::class);

        /** @var UserEntity $currentUser */
        $currentUser = m::mock(UserEntity::class)->makePartial();
        $currentUser->shouldReceive('isAnonymous')->andReturn(false);

        $this->mockedSmServices = [
            'SectionAccessService' => m::mock(),
            AuthorizationService::class => m::mock(AuthorizationService::class)
                ->shouldReceive('isGranted')->andReturn(false)->getMock(),
        ];

        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('getIdentity->getUser')
            ->andReturn($currentUser);

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Qry::create(['id' => 111]);

        /** @var LicenceEntity $licence */
        $licence = m::mock(LicenceEntity::class)->makePartial();
        $licence->setId(111);

        $licence->shouldReceive('serialize')
            ->andReturn(['foo' => 'bar'])
            ->shouldReceive('getNiFlag')
            ->andReturn('N')
            ->once()
            ->getMock()
            ->shouldReceive('getOrganisation')->andReturn(
                m::mock(Organisation::class)->shouldReceive('isMlh')->once()
                    ->andReturn(true)
                    ->getMock()
            )
            ->shouldReceive('isSpecialRestricted')
            ->andReturn(false)
            ->once();

        $mockContinuationDetail = m::mock(\Dvsa\Olcs\Api\Entity\Licence\ContinuationDetail::class)
            ->shouldReceive('serialize')->with(['continuation', 'licence'])->once()->andReturn(['CD'])
            ->getMock();
        $this->repoMap['ContinuationDetail']->shouldReceive('fetchForLicence')->with(111)
            ->andReturn([$mockContinuationDetail]);
        $this->repoMap['Note']
            ->shouldReceive('fetchForOverview')
            ->with(111)
            ->once()
            ->andReturn('latest note');

        $this->repoMap['Licence']->shouldReceive('fetchUsingId')
            ->with($query)
            ->andReturn($licence);

        $sections = ['bar', 'cake'];

        $this->mockedSmServices['SectionAccessService']->shouldReceive('getAccessibleSectionsForLicence')
            ->once()
            ->with($licence)
            ->andReturn($sections);

        $result = $this->sut->handleQuery($query);

        $expected = [
            'foo' => 'bar',
            'sections' => ['bar', 'cake'],
            'niFlag' => 'N',
            'isMlh' => true,
            'continuationMarker' => ['CD'],
            'latestNote' => 'latest note',
            'canHaveInspectionRequest' => true,
        ];

        $this->assertEquals($expected, $result->serialize());
    }

    public function testHandleQueryNoContinuationDetail()
    {
        $query = Qry::create(['id' => 111]);

        /** @var LicenceEntity $licence */
        $licence = m::mock(LicenceEntity::class)->makePartial();
        $licence->setId(111);

        $licence->shouldReceive('serialize')
            ->andReturn(['foo' => 'bar'])
            ->shouldReceive('getNiFlag')
            ->andReturn('N')
            ->once()
            ->getMock()
            ->shouldReceive('getOrganisation')->andReturn(
                m::mock(Organisation::class)->shouldReceive('isMlh')->once()
                    ->andReturn(true)
                    ->getMock()
            )
            ->shouldReceive('isSpecialRestricted')
            ->andReturn(true)
            ->once();

        $this->repoMap['ContinuationDetail']->shouldReceive('fetchForLicence')->with(111)
            ->andReturn([]);
        $this->repoMap['Note']
            ->shouldReceive('fetchForOverview')
            ->with(111)
            ->once()
            ->andReturn('latest note');

        $this->repoMap['Licence']->shouldReceive('fetchUsingId')
            ->with($query)
            ->andReturn($licence);

        $sections = ['bar', 'cake'];

        $this->mockedSmServices['SectionAccessService']->shouldReceive('getAccessibleSectionsForLicence')
            ->once()
            ->with($licence)
            ->andReturn($sections);

        $result = $this->sut->handleQuery($query);

        $expected = [
            'foo' => 'bar',
            'sections' => ['bar', 'cake'],
            'niFlag' => 'N',
            'isMlh' => true,
            'continuationMarker' => null,
            'latestNote' => 'latest note',
            'canHaveInspectionRequest' => false,
        ];

        $this->assertEquals($expected, $result->serialize());
    }
}

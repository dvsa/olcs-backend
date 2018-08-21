<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Licence;

use Dvsa\Olcs\Api\Domain\Exception\ForbiddenException;
use Dvsa\Olcs\Api\Entity\Licence\ContinuationDetail;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation;
use Dvsa\Olcs\Api\Entity\System\RefData;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\QueryHandler\Licence\Licence;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository;
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
        $this->mockRepo('Licence', Repository\Licence::class);
        $this->mockRepo('ContinuationDetail', Repository\ContinuationDetail::class);
        $this->mockRepo('Note', Repository\Note::class);
        $this->mockRepo('SystemParameter', Repository\SystemParameter::class);

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
            ->once()
            ->shouldReceive('getStatus->getId')
            ->andReturn(LicenceEntity::LICENCE_STATUS_VALID);

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
            'showExpiryWarning' => false,
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
            ->once()
            ->shouldReceive('getStatus->getId')
            ->andReturn(LicenceEntity::LICENCE_STATUS_VALID);

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
            'showExpiryWarning' => false,
        ];

        $this->assertEquals($expected, $result->serialize());
    }

    /**
     * @dataProvider testHandleQueryShowExpiryWarningDataProvider
     */
    public function testHandleQueryShowExpiryWarning($expected, $isExpiring, $isDisabled, $continuationDetailStatusId)
    {
        $query = Qry::create(['id' => 111]);

        /** @var LicenceEntity $licence */
        $licence = m::mock(LicenceEntity::class)->makePartial();
        $licence->setId(111);

        $continuationDetail = new ContinuationDetail();
        $continuationDetail->setStatus(new RefData($continuationDetailStatusId));

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
            ->shouldReceive('isSpecialRestricted')->andReturn(true)->once()
            ->shouldReceive('isExpiring')->with()->once()->andReturn($isExpiring)
            ->shouldReceive('getStatus->getId')
            ->andReturn(LicenceEntity::LICENCE_STATUS_VALID);

        $this->repoMap['ContinuationDetail']->shouldReceive('fetchForLicence')->with(111)
            ->andReturn([$continuationDetail]);
        $this->repoMap['Note']->shouldReceive('fetchForOverview')->with(111)->once()->andReturn('latest note');
        $this->repoMap['Licence']->shouldReceive('fetchUsingId')->with($query)->andReturn($licence);
        $this->repoMap['SystemParameter']->shouldReceive('getDisabledDigitalContinuations')->with()
            ->andReturn($isDisabled);

        $sections = ['bar', 'cake'];
        $this->mockedSmServices['SectionAccessService']->shouldReceive('getAccessibleSectionsForLicence')
            ->once()
            ->with($licence)
            ->andReturn($sections);

        $result = $this->sut->handleQuery($query);

        $expected = ['showExpiryWarning' => $expected];

        $this->assertArraySubset($expected, $result->serialize());
    }

    public function testHandleQueryShowExpiryWarningDataProvider()
    {
        return [
            'Should show' => [true, true, false, ContinuationDetail::STATUS_PRINTED],
            'Licence is expiring' => [false, false, false, ContinuationDetail::STATUS_PRINTED],
            'System Paramtere disabled' => [false, true, true, ContinuationDetail::STATUS_PRINTED],
            'Wrong Continuation detail status' => [false, true, false, ContinuationDetail::STATUS_PRINTING],
        ];
    }

    public function testHandleQueryShowExpiryWarningNoContinuationDetail()
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
            ->shouldReceive('isSpecialRestricted')->andReturn(true)->once()
            ->shouldReceive('getStatus->getId')
            ->andReturn(LicenceEntity::LICENCE_STATUS_VALID);

        $this->repoMap['ContinuationDetail']->shouldReceive('fetchForLicence')->with(111)
            ->andReturn([]);
        $this->repoMap['Note']->shouldReceive('fetchForOverview')->with(111)->once()->andReturn('latest note');
        $this->repoMap['Licence']->shouldReceive('fetchUsingId')->with($query)->andReturn($licence);

        $sections = ['bar', 'cake'];
        $this->mockedSmServices['SectionAccessService']->shouldReceive('getAccessibleSectionsForLicence')
            ->once()
            ->with($licence)
            ->andReturn($sections);

        $result = $this->sut->handleQuery($query);

        $expected = ['showExpiryWarning' => false];

        $this->assertArraySubset($expected, $result->serialize());
    }
}

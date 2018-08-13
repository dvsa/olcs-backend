<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Licence;

use Dvsa\Olcs\Api\Domain\Exception\ForbiddenException;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation;
use Mockery as m;
use Dvsa\Olcs\Api\Domain\QueryHandler\Licence\Licence;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository;
use Dvsa\Olcs\Transfer\Query\Licence\Licence as Qry;
use ZfcRbac\Service\AuthorizationService;
use Dvsa\OlcsTest\Api\Entity\User as UserEntity;

/**
 * Test Dvsa\Olcs\Api\Domain\QueryHandler\Licence\Licence mocking an External User
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
                ->shouldReceive('isGranted')
                ->with(\Dvsa\Olcs\Api\Entity\User\Permission::SELFSERVE_USER, null)
                ->andReturn(true)
                ->shouldReceive('isGranted')
                ->with(\Dvsa\Olcs\Api\Entity\User\Permission::INTERNAL_USER, null)
                ->andReturn(false)
                ->getMock(),
        ];

        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('getIdentity->getUser')
            ->andReturn($currentUser);

        parent::setUp();
    }

    public function testHandleQueryExternalUser()
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

    public function testHandleQueryExternalUserForbidden()
    {
        $this->expectException(ForbiddenException::class);
        $query = Qry::create(['id' => 111]);

        /** @var LicenceEntity $licence */
        $licence = m::mock(LicenceEntity::class)->makePartial();
        $licence->setId(111);

        $licence
            ->shouldReceive('getStatus->getId')
            ->once()
            ->andReturn(LicenceEntity::LICENCE_STATUS_REVOKED);

        $this->repoMap['Licence']->shouldReceive('fetchUsingId')
            ->with($query)
            ->andReturn($licence);

        $this->sut->handleQuery($query);
    }
}

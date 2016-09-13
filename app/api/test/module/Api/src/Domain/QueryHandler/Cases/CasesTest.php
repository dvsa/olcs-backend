<?php

namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\Cases;

use Dvsa\Olcs\Api\Domain\QueryHandler\Cases\Cases;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Dvsa\Olcs\Api\Domain\Repository\Cases as CasesRepo;
use Dvsa\Olcs\Api\Domain\Repository\Note as NoteRepo;
use Dvsa\Olcs\Transfer\Query\Cases\Cases as Qry;
use Dvsa\Olcs\Api\Entity\Cases\Cases as CasesEntity;
use Mockery as m;
use ZfcRbac\Service\AuthorizationService;
use Dvsa\OlcsTest\Api\Entity\User as UserEntity;

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

        /** @var UserEntity $currentUser */
        $currentUser = m::mock(UserEntity::class)->makePartial();
        $currentUser->shouldReceive('isAnonymous')->andReturn(false);

        $this->mockedSmServices = [
            AuthorizationService::class => m::mock(AuthorizationService::class)
                ->shouldReceive('isGranted')->andReturn(false)->getMock(),
        ];

        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('getIdentity->getUser')
            ->andReturn($currentUser);

        parent::setUp();
    }

    public function testHandleQueryLicenceCaseType()
    {
        $query = Qry::create(['id' => 24]);
        $licenceId = 77;
        $tmId = 2;
        $latestNote = 'test note';
        $caseType = CasesEntity::LICENCE_CASE_TYPE;

        $mockLicence = m::mock(\Dvsa\Olcs\Api\Entity\Licence\Licence::class);
        $mockLicence->shouldReceive('getId')->andReturn($licenceId);

        $mockTransportManager = m::mock(\Dvsa\Olcs\Api\Entity\Tm\TransportManager::class);
        $mockTransportManager->shouldReceive('getId')->andReturn($tmId);

        $mockCase = m::mock(\Dvsa\Olcs\Api\Entity\Cases\Cases::class);
        $mockCase->shouldReceive('serialize')->andReturn(['SERIALIZED']);
        $mockCase->shouldReceive('getCaseType')->andReturn($caseType);
        $mockCase->shouldReceive('getLicence')->andReturn($mockLicence);
        $mockCase->shouldReceive('getTransportManager')->andReturn($mockTransportManager);

        $this->repoMap['Cases']->shouldReceive('fetchUsingId')->with($query)->andReturn($mockCase);
        $this->repoMap['Note']->shouldReceive('fetchForOverview')
            ->with($licenceId)->andReturn($latestNote);

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

    public function testHandleQueryApplicationCaseType()
    {
        $query = Qry::create(['id' => 24]);
        $licenceId = 77;
        $applicationId = 4;
        $tmId = 2;
        $latestNote = 'test note';
        $caseType = CasesEntity::APP_CASE_TYPE;

        $mockLicence = m::mock(\Dvsa\Olcs\Api\Entity\Licence\Licence::class);
        $mockLicence->shouldReceive('getId')->andReturn($licenceId);

        $mockApplication = m::mock(\Dvsa\Olcs\Api\Entity\Application\Application::class);
        $mockApplication->shouldReceive('getId')->andReturn($applicationId);
        $mockApplication->shouldReceive('getLicence')->andReturn($mockLicence);

        $mockTransportManager = m::mock(\Dvsa\Olcs\Api\Entity\Tm\TransportManager::class);
        $mockTransportManager->shouldReceive('getId')->andReturn($tmId);

        $mockCase = m::mock(\Dvsa\Olcs\Api\Entity\Cases\Cases::class);
        $mockCase->shouldReceive('serialize')->andReturn(['SERIALIZED']);
        $mockCase->shouldReceive('getCaseType')->andReturn($caseType);
        $mockCase->shouldReceive('getApplication')
            ->andReturn($mockApplication);
        $mockCase->shouldReceive('getTransportManager')->andReturn($mockTransportManager);

        $this->repoMap['Cases']->shouldReceive('fetchUsingId')->with($query)->andReturn($mockCase);
        $this->repoMap['Note']->shouldReceive('fetchForOverview')
            ->with($licenceId)->andReturn($latestNote);

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


    public function testHandleQueryTmCaseType()
    {
        $query = Qry::create(['id' => 24]);
        $tmId = 2;
        $latestNote = 'test note';
        $caseType = CasesEntity::TM_CASE_TYPE;

        $mockTransportManager = m::mock(\Dvsa\Olcs\Api\Entity\Tm\TransportManager::class);
        $mockTransportManager->shouldReceive('getId')->andReturn($tmId);

        $mockCase = m::mock(\Dvsa\Olcs\Api\Entity\Cases\Cases::class);
        $mockCase->shouldReceive('serialize')->andReturn(['SERIALIZED']);
        $mockCase->shouldReceive('getCaseType')->andReturn($caseType);
        $mockCase->shouldReceive('getTransportManager')->andReturn($mockTransportManager);

        $this->repoMap['Cases']->shouldReceive('fetchUsingId')->with($query)->andReturn($mockCase);
        $this->repoMap['Note']->shouldReceive('fetchForOverview')
            ->with(null, null, $tmId)->andReturn($latestNote);

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

    public function testHandleQueryOtherCaseType()
    {
        $query = Qry::create(['id' => 24]);
        $tmId = 2;
        $latestNote = 'test note';
        $caseType = 'some-other';

        $mockTransportManager = m::mock(\Dvsa\Olcs\Api\Entity\Tm\TransportManager::class);
        $mockTransportManager->shouldReceive('getId')->andReturn($tmId);

        $mockCase = m::mock(\Dvsa\Olcs\Api\Entity\Cases\Cases::class);
        $mockCase->shouldReceive('serialize')->andReturn(['SERIALIZED']);
        $mockCase->shouldReceive('getCaseType')->andReturn($caseType);

        $this->repoMap['Cases']->shouldReceive('fetchUsingId')->with($query)->andReturn($mockCase);
        $this->repoMap['Note']->shouldReceive('fetchForOverview')
            ->with(null, null, null, m::type('string'))->andReturn($latestNote);

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

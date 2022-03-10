<?php

/**
 * MyAccount Test
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\MyAccount;

use Dvsa\Olcs\Api\Domain\QueryHandler\Result;
use Dvsa\Olcs\Api\Entity\System\SystemParameter;
use Dvsa\Olcs\Api\Entity\User\Team;
use Dvsa\Olcs\Transfer\Service\CacheEncryption;
use Mockery as m;
use Dvsa\Olcs\Api\Entity\User\User;
use ZfcRbac\Service\AuthorizationService;
use Dvsa\Olcs\Api\Domain\Exception\NotFoundException;
use Dvsa\Olcs\Transfer\Query\MyAccount\MyAccount as Qry;
use Dvsa\Olcs\Api\Domain\QueryHandler\MyAccount\MyAccount;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;

/**
 * MyAccount Test
 */
class MyAccountTest extends QueryHandlerTestCase
{
    public function setUp(): void
    {
        $this->sut = new MyAccount();

        $this->mockedSmServices = [
            AuthorizationService::class => m::mock(AuthorizationService::class),
            CacheEncryption::class => m::mock(CacheEncryption::class),
        ];

        parent::setUp();
    }

    /**
     * @dataProvider dpUserIdProvider
     */
    public function testHandleQueryFromCache($userId, $searchUserId)
    {
        $cacheResult = ['result'];

        /** @var User $mockUser */
        $mockUser = m::mock(User::class);
        $mockUser->expects('getId')->andReturn($userId);

        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('getIdentity->getUser')
            ->andReturn($mockUser);

        $this->mockedSmServices[CacheEncryption::class]->expects('hasCustomItem')
            ->with(CacheEncryption::USER_ACCOUNT_IDENTIFIER, $searchUserId)
            ->andReturnTrue();

        $this->mockedSmServices[CacheEncryption::class]->expects('getCustomItem')
            ->with(CacheEncryption::USER_ACCOUNT_IDENTIFIER, $searchUserId)
            ->andReturn($cacheResult);

        $query = Qry::create([]);

        $this->assertEquals(
            $cacheResult,
            $this->sut->handleQuery($query)
        );
    }

    public function dpUserIdProvider()
    {
        return [
            [999, 999],
            [null, 'anon'],
        ];
    }

    /**
     * @dataProvider dpHandleQuery
     */
    public function testHandleQuery($isSelfservePromptEnabled, $isEligibleForPermits, $expectedEligibleForPrompt)
    {
        $userId = 1;

        /** @var User $mockUser */
        $mockUser = m::mock(User::class)->makePartial();
        $mockUser->setId($userId);
        $mockUser->expects('isInternal')->withNoArgs()->andReturnFalse();
        $mockUser->shouldReceive('serialize')->andReturn(['foo']);
        $mockUser->shouldReceive('hasActivePsvLicence')->andReturn(false);
        $mockUser->shouldReceive('getNumberOfVehicles')->andReturn(2);
        $mockUser->shouldReceive('hasOrganisationSubmittedLicenceApplication')->andReturn(true);
        $mockUser->expects('isEligibleForPermits')->withNoArgs()->andReturn($isEligibleForPermits);

        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('getIdentity->getUser')
            ->andReturn($mockUser);

        $this->mockedSmServices[CacheEncryption::class]->expects('hasCustomItem')
            ->with(CacheEncryption::USER_ACCOUNT_IDENTIFIER, $userId)
            ->andReturnFalse();

        $numCacheCalls = $isEligibleForPermits ? 1 : 0;

        $mockSystemParam = m::mock(SystemParameter::class);
        $mockSystemParam->expects('getParamValue')
            ->withNoArgs()
            ->times($numCacheCalls)
            ->andReturn($isSelfservePromptEnabled);

        $sysParamResult = new Result($mockSystemParam);

        $this->expectedCacheCall(
            CacheEncryption::SYS_PARAM_IDENTIFIER,
            SystemParameter::ENABLE_SELFSERVE_PROMPT,
            $sysParamResult,
            $numCacheCalls
        );

        $query = Qry::create([]);

        $expectedResult = [
            'foo',
            'hasActivePsvLicence' => false,
            'numberOfVehicles' => 2,
            'hasOrganisationSubmittedLicenceApplication' => true,
            'eligibleForPermits' => $isEligibleForPermits,
            'eligibleForPrompt' => $expectedEligibleForPrompt,
            'dataAccess' => [],
            'isInternal' => false,
        ];

        $this->mockedSmServices[CacheEncryption::class]->expects('setCustomItem')
            ->with(CacheEncryption::USER_ACCOUNT_IDENTIFIER, $expectedResult, $userId);

        $result = $this->sut->handleQuery($query);
        $this->assertEquals(
            $expectedResult,
            $result->serialize()
        );
    }

    public function testHandleQueryInternal()
    {
        $userId = 1;
        $canAccessAll = true;
        $canAccessGb = true;
        $canAccessNi = true;
        $trafficAreas = ['B', 'C'];

        $dataAccess = [
            'canAccessAll' => $canAccessAll,
            'canAccessGb' => $canAccessGb,
            'canAccessNi' => $canAccessNi,
            'trafficAreas' => $trafficAreas,
        ];

        $excludedTeamsString = '1, 2, 3';
        $excludedTeamsArray = [1, 2, 3];

        $mockSystemParam = m::mock(SystemParameter::class);
        $mockSystemParam->expects('getParamValue')->andReturn($excludedTeamsString);

        $sysParamResult = new Result($mockSystemParam);

        $this->expectedCacheCall(
            CacheEncryption::SYS_PARAM_IDENTIFIER,
            SystemParameter::DATA_SEPARATION_TEAMS_EXEMPT,
            $sysParamResult
        );

        $mockTeam = m::mock(Team::class);
        $mockTeam->expects('canAccessAllData')->with($excludedTeamsArray)->andReturn($canAccessAll);
        $mockTeam->expects('canAccessGbData')->with($excludedTeamsArray)->andReturn($canAccessGb);
        $mockTeam->expects('canAccessNiData')->with($excludedTeamsArray)->andReturn($canAccessNi);
        $mockTeam->expects('getAllowedTrafficAreas')->with($excludedTeamsArray)->andReturn($trafficAreas);

        /** @var User $mockUser */
        $mockUser = m::mock(User::class)->makePartial();
        $mockUser->setId($userId);
        $mockUser->expects('isInternal')->withNoArgs()->andReturnTrue();
        $mockUser->shouldReceive('serialize')->andReturn(['foo']);
        $mockUser->shouldReceive('hasActivePsvLicence')->never();
        $mockUser->shouldReceive('getNumberOfVehicles')->never();
        $mockUser->shouldReceive('hasOrganisationSubmittedLicenceApplication')->never();
        $mockUser->expects('isEligibleForPermits')->never();
        $mockUser->expects('getTeam')->andReturn($mockTeam);

        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('getIdentity->getUser')
            ->andReturn($mockUser);

        $this->mockedSmServices[CacheEncryption::class]->expects('hasCustomItem')
            ->with(CacheEncryption::USER_ACCOUNT_IDENTIFIER, $userId)
            ->andReturnFalse();

        $query = Qry::create([]);

        $expectedResult = [
            'foo',
            'hasActivePsvLicence' => false,
            'numberOfVehicles' => 0,
            'hasOrganisationSubmittedLicenceApplication' => false,
            'eligibleForPermits' => false,
            'eligibleForPrompt' => false,
            'dataAccess' => $dataAccess,
            'isInternal' => true,
        ];

        $this->mockedSmServices[CacheEncryption::class]->expects('setCustomItem')
            ->with(CacheEncryption::USER_ACCOUNT_IDENTIFIER, $expectedResult, $userId);

        $result = $this->sut->handleQuery($query);
        $this->assertEquals(
            $expectedResult,
            $result->serialize()
        );
    }

    public function dpHandleQuery()
    {
        return [
            [
                'isSelfservePromptEnabled' => false,
                'isEligibleForPermits' => true,
                'expectedEligibleForPrompt' => false,
            ],
            [
                'isSelfservePromptEnabled' => true,
                'isEligibleForPermits' => false,
                'expectedEligibleForPrompt' => false,
            ],
            [
                'isSelfservePromptEnabled' => true,
                'isEligibleForPermits' => true,
                'expectedEligibleForPrompt' => true,
            ],
        ];
    }

    public function testHandleQueryThrowsNotFoundException()
    {
        $this->expectException(NotFoundException::class);

        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('getIdentity->getUser')
            ->andReturn(null);

        $query = Qry::create([]);

        $this->sut->handleQuery($query);
    }
}

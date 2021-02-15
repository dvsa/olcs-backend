<?php

/**
 * MyAccount Test
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\MyAccount;

use Dvsa\Olcs\Transfer\Service\CacheEncryption;
use Mockery as m;
use Dvsa\Olcs\Api\Entity\User\User;
use ZfcRbac\Service\AuthorizationService;
use Dvsa\Olcs\Api\Domain\Repository\SystemParameter;
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

        $this->mockRepo('SystemParameter', SystemParameter::class);

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
        $mockUser->shouldReceive('serialize')->andReturn(['foo']);
        $mockUser->shouldReceive('hasActivePsvLicence')->andReturn(false);
        $mockUser->shouldReceive('getNumberOfVehicles')->andReturn(2);
        $mockUser->expects('isEligibleForPermits')->withNoArgs()->andReturn($isEligibleForPermits);

        $this->mockedSmServices[AuthorizationService::class]->shouldReceive('getIdentity->getUser')
            ->andReturn($mockUser);

        $this->mockedSmServices[CacheEncryption::class]->expects('hasCustomItem')
            ->with(CacheEncryption::USER_ACCOUNT_IDENTIFIER, $userId)
            ->andReturnFalse();

        $mockSystemParameter = $this->repoMap['SystemParameter'];
        $mockSystemParameter->shouldReceive('isSelfservePromptEnabled')
            ->times($isEligibleForPermits ? 1 : 0)
            ->withNoArgs()
            ->andReturn($isSelfservePromptEnabled);

        $query = Qry::create([]);

        $expectedResult = [
            'foo',
            'hasActivePsvLicence' => false,
            'numberOfVehicles' => 2,
            'eligibleForPermits' => $isEligibleForPermits,
            'eligibleForPrompt' => $expectedEligibleForPrompt,
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

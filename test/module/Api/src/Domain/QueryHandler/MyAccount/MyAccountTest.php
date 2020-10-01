<?php

/**
 * MyAccount Test
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\MyAccount;

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
            AuthorizationService::class => m::mock(AuthorizationService::class)
        ];

        $this->mockRepo('SystemParameter', SystemParameter::class);

        parent::setUp();
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

        $mockSystemParameter = $this->repoMap['SystemParameter'];
        $mockSystemParameter->shouldReceive('getDisableDataRetentionRecords')->andReturn(true);
        $mockSystemParameter->shouldReceive('isSelfservePromptEnabled')
            ->times($isEligibleForPermits ? 1 : 0)
            ->withNoArgs()
            ->andReturn($isSelfservePromptEnabled);

        $query = Qry::create([]);

        $result = $this->sut->handleQuery($query);
        $this->assertEquals(
            [
                'foo',
                'hasActivePsvLicence' => false,
                'numberOfVehicles' => 2,
                'disableDataRetentionRecords' => true,
                'eligibleForPermits' => $isEligibleForPermits,
                'eligibleForPrompt' => $expectedEligibleForPrompt,
            ],
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

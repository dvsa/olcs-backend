<?php

/**
 * Pid Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\OlcsTest\Api\Domain\QueryHandler\User;

use Dvsa\Olcs\Api\Domain\QueryHandler\User\Pid as QueryHandler;
use Dvsa\Olcs\Api\Domain\Repository\User as Repo;
use Dvsa\Olcs\Api\Service\OpenAm\UserInterface;
use Dvsa\Olcs\Transfer\Query\User\User as Query;
use Dvsa\OlcsTest\Api\Domain\QueryHandler\QueryHandlerTestCase;
use Mockery as m;

/**
 * Pid Test
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
class PidTest extends QueryHandlerTestCase
{
    public function setUp(): void
    {
        $this->sut = new QueryHandler();
        $this->mockRepo('User', Repo::class);

        $this->mockedSmServices = [
            UserInterface::class => m::mock(UserInterface::class)
        ];

        parent::setUp();
    }

    /**
     * @dataProvider handleQueryDataProvider
     */
    public function testHandleQuery($isInternal, $userId, $isActiveUser, $expectedCanResetPassword)
    {
        $query = Query::create(['id' => 'login_id']);

        $mockUser = m::mock(\Dvsa\Olcs\Api\Entity\User\User::class);
        $mockUser->shouldReceive('getId')->andReturn($userId);
        $mockUser->shouldReceive('getPid')->andReturn('some-pid');
        $mockUser->shouldReceive('isInternal')->andReturn($isInternal);

        $this->repoMap['User']->shouldReceive('fetchOneByLoginId')->with('login_id')->andReturn($mockUser);

        $this->mockedSmServices[UserInterface::class]->shouldReceive('isActiveUser')
            ->with('some-pid')
            ->andReturn($isActiveUser);

        $result = $this->sut->handleQuery($query);

        $this->assertSame(
            [
                'pid' => 'some-pid',
                'canResetPassword' => $expectedCanResetPassword
            ],
            $result
        );
    }

    public function handleQueryDataProvider()
    {
        return [
            // selfserve - migrated user - inactive - can reset password
            [false, (QueryHandler::CAN_RESET_PWD_IF_NOT_ACTIVE_MAX_USER_ID - 1), false, true],
            // selfserve - migrated user - active - can reset password
            [false, (QueryHandler::CAN_RESET_PWD_IF_NOT_ACTIVE_MAX_USER_ID - 1), true, true],
            // selfserve - non-migrated user - inactive - cannot reset password
            [false, QueryHandler::CAN_RESET_PWD_IF_NOT_ACTIVE_MAX_USER_ID, false, false],
            // selfserve - non-migrated user - active - can reset password
            [false, QueryHandler::CAN_RESET_PWD_IF_NOT_ACTIVE_MAX_USER_ID, true, true],

            // internal - migrated user - inactive - cannot reset password
            [true, (QueryHandler::CAN_RESET_PWD_IF_NOT_ACTIVE_MAX_USER_ID - 1), false, false],
            // internal - migrated user - active - can reset password
            [true, (QueryHandler::CAN_RESET_PWD_IF_NOT_ACTIVE_MAX_USER_ID - 1), true, true],
            // internal - non-migrated user - inactive - cannot reset password
            [true, QueryHandler::CAN_RESET_PWD_IF_NOT_ACTIVE_MAX_USER_ID, false, false],
            // internal - non-migrated user - active - can reset password
            [true, QueryHandler::CAN_RESET_PWD_IF_NOT_ACTIVE_MAX_USER_ID, true, true],
        ];
    }
}

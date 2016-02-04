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
    public function setUp()
    {
        $this->sut = new QueryHandler();
        $this->mockRepo('User', Repo::class);

        $this->mockedSmServices = [
            UserInterface::class => m::mock(UserInterface::class)
        ];

        parent::setUp();
    }

    public function testHandleQuery()
    {
        $query = Query::create(['id' => 'login_id']);

        $mockUser = m::mock(\Dvsa\Olcs\Api\Entity\User\User::class);
        $mockUser->shouldReceive('getPid')->andReturn('some-pid');

        $this->repoMap['User']->shouldReceive('fetchOneByLoginId')->with('login_id')->andReturn($mockUser);

        $this->mockedSmServices[UserInterface::class]->shouldReceive('isActiveUser')
            ->once()
            ->with('some-pid')
            ->andReturn(false);

        $result = $this->sut->handleQuery($query);

        $this->assertSame(['pid' => 'some-pid', 'isActive' => false], $result);
    }
}

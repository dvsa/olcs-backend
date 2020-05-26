<?php

namespace Dvsa\OlcsTest\Cli\Domain\CommandHandler;

use ArrayIterator;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Dvsa\Olcs\Api\Domain\Repository;
use Dvsa\Olcs\Api\Service\OpenAm\User;
use Dvsa\Olcs\Api\Service\OpenAm\UserInterface;
use Dvsa\Olcs\Cli\Domain\Command\PopulateLastLoginFromOpenAm as PopulateLastLoginFromOpenAmCmd;
use Dvsa\Olcs\Cli\Domain\CommandHandler\PopulateLastLoginFromOpenAm;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Mockery as m;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\NullOutput;

class PopulateLastLoginFromOpenAmTest extends CommandHandlerTestCase
{
    protected $mockOpenAmUserService;

    public function setUp()
    {
        $this->sut = new PopulateLastLoginFromOpenAm();

        $this->mockRepo('User', Repository\User::class);
        $this->mockRepo('Document', Repository\Document::class);

        $this->mockOpenAmUserService = m::mock(User::class);

        $this->mockedSmServices = [
            UserInterface::class => $this->mockOpenAmUserService
        ];

        parent::setUp();
    }

    public function testHandleCommand()
    {
        $totalNumberOfUsers = 4;
        $batchSize = 2;

        $params = [
            'isLiveRun' => true,
            'batchSize' => $batchSize,
            'limit' => -1,
            'progressBar' => $this->makeProgressBar()
        ];

        $this->repoMap['User']->shouldReceive('fetchActiveUserCount')
            ->andReturn($totalNumberOfUsers);

        $this->repoMap['User']->shouldReceive('fetchPaginatedActiveUsers')
            ->with(0, 2)
            ->andReturn($this->iterableListOfUsers('batch1', $batchSize));

        $this->repoMap['User']->shouldReceive('fetchPaginatedActiveUsers')
            ->with(2, 2)
            ->andReturn($this->iterableListOfUsers('batch2', $batchSize));

        $this->repoMap['User']->shouldReceive('saveOnFlush')->times($totalNumberOfUsers);
        $this->repoMap['User']->shouldReceive('flushAll')->times($batchSize);

        $this->mockOpenAmUserService
            ->shouldReceive('fetchUsers')
            ->with(['batch1-pid-1', 'batch1-pid-2'])
            ->andReturn(
                [
                    [
                        'pid' => 'batch1-pid-1',
                        'lastLoginTime' => '2020-01-01 10:00:00'
                    ],
                    [
                        'pid' => 'batch1-pid-2',
                        'lastLoginTime' => '2020-01-01 11:00:00'
                    ]
                ]
            );
        $this->mockOpenAmUserService
            ->shouldReceive('fetchUsers')
            ->with(['batch2-pid-1', 'batch2-pid-2'])
            ->andReturn(
                [
                    [
                        'pid' => 'batch2-pid-1',
                        'lastLoginTime' => '2020-01-02 10:00:00'
                    ],
                    [
                        'pid' => 'batch2-pid-2',
                        'lastLoginTime' => '2020-01-02 11:00:00'
                    ]
                ]
            );

        $result = $this->sut->handleCommand(PopulateLastLoginFromOpenAmCmd::create($params))->toArray();

        $messages = $result["messages"];

        $this->assertContains("[Batch 1] Setting last login time for user 'batch1-loginId-1' to '2020-01-01 10:00:00'", $messages);
        $this->assertContains("[Batch 1] Setting last login time for user 'batch1-loginId-2' to '2020-01-01 11:00:00'", $messages);
        $this->assertContains("[Batch 2] Setting last login time for user 'batch2-loginId-1' to '2020-01-02 10:00:00'", $messages);
        $this->assertContains("[Batch 2] Setting last login time for user 'batch2-loginId-2' to '2020-01-02 11:00:00'", $messages);
    }

    public function testHandleCommandWithOpenAMError()
    {
        $totalNumberOfUsers = 4;
        $batchSize = 2;

        $params = [
            'isLiveRun' => true,
            'batchSize' => $batchSize,
            'limit' => -1
        ];

        $this->repoMap['User']->shouldReceive('fetchActiveUserCount')
            ->andReturn($totalNumberOfUsers);

        $this->repoMap['User']->shouldReceive('fetchPaginatedActiveUsers')
            ->with(0, 2)
            ->andReturn($this->iterableListOfUsers('batch1', $batchSize));

        $this->repoMap['User']->shouldReceive('fetchPaginatedActiveUsers')
            ->with(2, 2)
            ->andReturn($this->iterableListOfUsers('batch2', $batchSize));

        $this->repoMap['User']->shouldReceive('saveOnFlush')->never();
        $this->repoMap['User']->shouldReceive('flushAll')->never();

        $this->mockOpenAmUserService
            ->shouldReceive('fetchUsers')
            ->andThrow(new \Exception("Exception from OpenAM"));

        $result = $this->sut->handleCommand(PopulateLastLoginFromOpenAmCmd::create($params))->toArray();

        $messages = $result["messages"];

        $this->assertContains("[Batch 1] Unable to get OpenAM data. Error : Exception from OpenAM", $messages);
        $this->assertContains("[Batch 2] Unable to get OpenAM data. Error : Exception from OpenAM", $messages);
    }

    public function testHandleCommandWithUsersMissingInOpenAM()
    {
        $totalNumberOfUsers = 4;
        $totalNumberOfUsersInOpenAM = 2;
        $batchSize = 2;

        $params = [
            'isLiveRun' => true,
            'batchSize' => $batchSize,
            'limit' => -1
        ];

        $this->repoMap['User']->shouldReceive('fetchActiveUserCount')
            ->andReturn($totalNumberOfUsers);

        $this->repoMap['User']->shouldReceive('fetchPaginatedActiveUsers')
            ->with(0, 2)
            ->andReturn($this->iterableListOfUsers('batch1', $batchSize));

        $this->repoMap['User']->shouldReceive('fetchPaginatedActiveUsers')
            ->with(2, 2)
            ->andReturn($this->iterableListOfUsers('batch2', $batchSize));

        $this->repoMap['User']->shouldReceive('saveOnFlush')->times($totalNumberOfUsersInOpenAM);
        $this->repoMap['User']->shouldReceive('flushAll')->times($batchSize);

        $this->mockOpenAmUserService
            ->shouldReceive('fetchUsers')
            ->with(['batch1-pid-1', 'batch1-pid-2'])
            ->andReturn(
                [
                    [
                        'pid' => 'batch1-pid-1',
                        'lastLoginTime' => '2020-01-01 10:00:00'
                    ]
                ]
            );
        $this->mockOpenAmUserService
            ->shouldReceive('fetchUsers')
            ->with(['batch2-pid-1', 'batch2-pid-2'])
            ->andReturn(
                [
                    [
                        'pid' => 'batch2-pid-1',
                        'lastLoginTime' => '2020-01-02 10:00:00'
                    ]
                ]
            );

        $result = $this->sut->handleCommand(PopulateLastLoginFromOpenAmCmd::create($params))->toArray();

        $messages = $result["messages"];

        $this->assertContains("[Batch 1] Could not find user 'batch1-loginId-2' in OpenAM", $messages);
        $this->assertContains("[Batch 2] Could not find user 'batch2-loginId-2' in OpenAM", $messages);
    }

    public function testHandleCommandWithNoLastLoginTimeInOpenAM()
    {
        $totalNumberOfUsers = 2;
        $totalNumberOfUsersWithLastLoginTimeInOpenAM = 1;
        $batchSize = 2;

        $params = [
            'isLiveRun' => true,
            'batchSize' => $batchSize,
            'limit' => -1
        ];

        $this->repoMap['User']->shouldReceive('fetchActiveUserCount')
            ->andReturn($totalNumberOfUsers);

        $this->repoMap['User']->shouldReceive('fetchPaginatedActiveUsers')
            ->with(0, 2)
            ->andReturn($this->iterableListOfUsers('batch1', $batchSize));

        $this->repoMap['User']->shouldReceive('saveOnFlush')->times($totalNumberOfUsersWithLastLoginTimeInOpenAM);
        $this->repoMap['User']->shouldReceive('flushAll')->times(1);

        $this->mockOpenAmUserService
            ->shouldReceive('fetchUsers')
            ->with(['batch1-pid-1', 'batch1-pid-2'])
            ->andReturn(
                [
                    [
                        'pid' => 'batch1-pid-1',
                        'lastLoginTime' => '2020-01-01 10:00:00'
                    ],
                    [
                        'pid' => 'batch1-pid-2'
                    ]
                ]
            );

        $result = $this->sut->handleCommand(PopulateLastLoginFromOpenAmCmd::create($params))->toArray();

        $messages = $result["messages"];

        $this->assertContains("[Batch 1] No last login time found for user 'batch1-loginId-2'", $messages);
    }

    public function testHandleCommandWithCustomLimit()
    {
        $totalNumberOfUsers = 4;
        $batchSize = 2;

        $params = [
            'isLiveRun' => true,
            'batchSize' => $batchSize,
            'limit' => 4
        ];

        $this->repoMap['User']->shouldReceive('fetchActiveUserCount')
            ->never();

        $this->repoMap['User']->shouldReceive('fetchPaginatedActiveUsers')
            ->with(0, 2)
            ->andReturn($this->iterableListOfUsers('batch1', $batchSize));

        $this->repoMap['User']->shouldReceive('fetchPaginatedActiveUsers')
            ->with(2, 2)
            ->andReturn($this->iterableListOfUsers('batch2', $batchSize));

        $this->repoMap['User']->shouldReceive('saveOnFlush')->times($totalNumberOfUsers);
        $this->repoMap['User']->shouldReceive('flushAll')->times($batchSize);

        $this->mockOpenAmUserService
            ->shouldReceive('fetchUsers')
            ->with(['batch1-pid-1', 'batch1-pid-2'])
            ->andReturn(
                [
                    [
                        'pid' => 'batch1-pid-1',
                        'lastLoginTime' => '2020-01-01 10:00:00'
                    ],
                    [
                        'pid' => 'batch1-pid-2',
                        'lastLoginTime' => '2020-01-01 11:00:00'
                    ]
                ]
            );
        $this->mockOpenAmUserService
            ->shouldReceive('fetchUsers')
            ->with(['batch2-pid-1', 'batch2-pid-2'])
            ->andReturn(
                [
                    [
                        'pid' => 'batch2-pid-1',
                        'lastLoginTime' => '2020-01-02 10:00:00'
                    ],
                    [
                        'pid' => 'batch2-pid-2',
                        'lastLoginTime' => '2020-01-02 11:00:00'
                    ]
                ]
            );

        $result = $this->sut->handleCommand(PopulateLastLoginFromOpenAmCmd::create($params))->toArray();

        $messages = $result["messages"];

        $this->assertContains("[Batch 1] Setting last login time for user 'batch1-loginId-1' to '2020-01-01 10:00:00'", $messages);
        $this->assertContains("[Batch 1] Setting last login time for user 'batch1-loginId-2' to '2020-01-01 11:00:00'", $messages);
        $this->assertContains("[Batch 2] Setting last login time for user 'batch2-loginId-1' to '2020-01-02 10:00:00'", $messages);
        $this->assertContains("[Batch 2] Setting last login time for user 'batch2-loginId-2' to '2020-01-02 11:00:00'", $messages);
    }

    public function testHandleCommandInDryRunMode()
    {
        $totalNumberOfUsers = 4;
        $batchSize = 2;

        $params = [
            'isLiveRun' => false,
            'batchSize' => $batchSize,
            'limit' => -1,
            'progressBar' => $this->makeProgressBar()
        ];

        $this->repoMap['User']->shouldReceive('fetchActiveUserCount')
            ->andReturn($totalNumberOfUsers);

        $this->repoMap['User']->shouldReceive('fetchPaginatedActiveUsers')
            ->with(0, 2)
            ->andReturn($this->iterableListOfUsers('batch1', $batchSize));

        $this->repoMap['User']->shouldReceive('fetchPaginatedActiveUsers')
            ->with(2, 2)
            ->andReturn($this->iterableListOfUsers('batch2', $batchSize));

        $this->repoMap['User']->shouldReceive('saveOnFlush')->times($totalNumberOfUsers);
        $this->repoMap['User']->shouldReceive('flushAll')->never();

        $this->mockOpenAmUserService
            ->shouldReceive('fetchUsers')
            ->with(['batch1-pid-1', 'batch1-pid-2'])
            ->andReturn(
                [
                    [
                        'pid' => 'batch1-pid-1',
                        'lastLoginTime' => '2020-01-01 10:00:00'
                    ],
                    [
                        'pid' => 'batch1-pid-2',
                        'lastLoginTime' => '2020-01-01 11:00:00'
                    ]
                ]
            );
        $this->mockOpenAmUserService
            ->shouldReceive('fetchUsers')
            ->with(['batch2-pid-1', 'batch2-pid-2'])
            ->andReturn(
                [
                    [
                        'pid' => 'batch2-pid-1',
                        'lastLoginTime' => '2020-01-02 10:00:00'
                    ],
                    [
                        'pid' => 'batch2-pid-2',
                        'lastLoginTime' => '2020-01-02 11:00:00'
                    ]
                ]
            );

        $result = $this->sut->handleCommand(PopulateLastLoginFromOpenAmCmd::create($params))->toArray();

        $messages = $result["messages"];

        $this->assertContains("[Batch 1] Setting last login time for user 'batch1-loginId-1' to '2020-01-01 10:00:00'", $messages);
        $this->assertContains("[Batch 1] Setting last login time for user 'batch1-loginId-2' to '2020-01-01 11:00:00'", $messages);
        $this->assertContains("[Batch 1] Dry run mode. Skipping database update", $messages);
        $this->assertContains("[Batch 2] Setting last login time for user 'batch2-loginId-1' to '2020-01-02 10:00:00'", $messages);
        $this->assertContains("[Batch 2] Setting last login time for user 'batch2-loginId-2' to '2020-01-02 11:00:00'", $messages);
        $this->assertContains("[Batch 2] Dry run mode. Skipping database update", $messages);
    }

    private function iterableListOfUsers($prefix, $count)
    {
        $users = [];

        for ($i=1; $i<=$count; $i++) {
            $mockUser = m::mock(\Dvsa\Olcs\Api\Entity\User\User::class);
            $mockUser->shouldReceive('getPid')
                ->andReturn($prefix . '-pid-' . $i);
            $mockUser->shouldReceive('getLoginId')
                ->andReturn($prefix. '-loginId-' . $i);
            $mockUser->shouldReceive('setLastLoginAt');

            $users[] = $mockUser;
        }

        $paginator = m::mock(Paginator::class);
        $paginator->shouldReceive('getIterator')->andReturn(new ArrayIterator($users));

        return $paginator;
    }

    private function makeProgressBar()
    {
        return new ProgressBar(new NullOutput());
    }
}

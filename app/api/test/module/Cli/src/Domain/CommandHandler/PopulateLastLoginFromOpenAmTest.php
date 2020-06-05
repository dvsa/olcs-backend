<?php

namespace Dvsa\OlcsTest\Cli\Domain\CommandHandler;

use ArrayIterator;
use Dvsa\Olcs\Api\Domain\Repository;
use Dvsa\Olcs\Api\Service\OpenAm\User;
use Dvsa\Olcs\Api\Service\OpenAm\UserInterface;
use Dvsa\Olcs\Cli\Domain\Command\PopulateLastLoginFromOpenAm as PopulateLastLoginFromOpenAmCmd;
use Dvsa\Olcs\Cli\Domain\CommandHandler\PopulateLastLoginFromOpenAm;
use Dvsa\OlcsTest\Api\Domain\CommandHandler\CommandHandlerTestCase;
use Exception;
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

        $this->mockUserRepoThatSavesAllUsers($totalNumberOfUsers, $batchSize);
        $this->mockOpenAMWithUsers();

        $params = [
            'isLiveRun' => true,
            'batchSize' => $batchSize,
            'progressBar' => $this->makeProgressBar()
        ];

        $expectedOutputMessages = [
            "[Batch 1] Setting last login time for user 'loginId-1' to '2020-01-01 10:00:00'",
            "[Batch 1] Setting last login time for user 'loginId-2' to '2020-01-01 11:00:00'",
            "[Batch 2] Setting last login time for user 'loginId-3' to '2020-01-02 10:00:00'",
            "[Batch 2] Setting last login time for user 'loginId-4' to '2020-01-02 11:00:00'"
        ];

        $this->runCommandAndAssertOutput($params, $expectedOutputMessages);
    }

    public function testHandleCommandWithOpenAMError()
    {
        $totalNumberOfUsers = 4;
        $batchSize = 2;

        $this->mockUserRepoThatDoesNotSaveUsers($totalNumberOfUsers);
        $this->mockOpenAMWithException();

        $params = [
            'isLiveRun' => true,
            'batchSize' => $batchSize
        ];

        $expectedOutputMessages = [
            "[Batch 1] Unable to process batch. Error : Exception from OpenAM",
            "[Batch 2] Unable to process batch. Error : Exception from OpenAM"
        ];

        $this->runCommandAndAssertOutput($params, $expectedOutputMessages);
    }

    public function testHandleCommandWithUsersMissingInOpenAM()
    {
        $totalNumberOfUsers = 4;
        $totalNumberOfUsersInOpenAM = 2;
        $batchSize = 2;

        $this->mockUserRepoThatSavesSomeUsers($totalNumberOfUsers, $batchSize, $totalNumberOfUsersInOpenAM);

        $this->mockOpenAMWithMissingUsers();

        $params = [
            'isLiveRun' => true,
            'batchSize' => $batchSize
        ];

        $expectedOutputMessages = [
            "[Batch 1] Could not find user 'loginId-2' in OpenAM",
            "[Batch 2] Could not find user 'loginId-4' in OpenAM"
        ];

        $this->runCommandAndAssertOutput($params, $expectedOutputMessages);
    }

    public function testHandleCommandWithNoLastLoginTimeInOpenAM()
    {
        $totalNumberOfUsers = 4;
        $totalNumberOfUsersWithLastLoginTimeInOpenAM = 2;
        $batchSize = 2;

        $this->mockUserRepoThatSavesSomeUsers($totalNumberOfUsers, $batchSize, $totalNumberOfUsersWithLastLoginTimeInOpenAM);
        $this->mockOpenAMWithNoLastLoginTime();

        $params = [
            'isLiveRun' => true,
            'batchSize' => $batchSize,
        ];

        $expectedOutputMessages = [
            "[Batch 1] No last login time found for user 'loginId-2'",
            "[Batch 2] No last login time found for user 'loginId-4'"
        ];

        $this->runCommandAndAssertOutput($params, $expectedOutputMessages);
    }

    public function testHandleCommandWithCustomLimit()
    {
        $totalNumberOfUsers = 4;
        $batchSize = 2;

        $this->mockUserRepoThatSavesAllUsers($totalNumberOfUsers, $batchSize);

        $this->mockOpenAMWithUsers();

        $params = [
            'isLiveRun' => true,
            'batchSize' => $batchSize,
            'limit' => 4
        ];

        $expectedOutputMessages = [
            "[Batch 1] Setting last login time for user 'loginId-1' to '2020-01-01 10:00:00'",
            "[Batch 1] Setting last login time for user 'loginId-2' to '2020-01-01 11:00:00'",
            "[Batch 2] Setting last login time for user 'loginId-3' to '2020-01-02 10:00:00'",
            "[Batch 2] Setting last login time for user 'loginId-4' to '2020-01-02 11:00:00'"
        ];

        $this->runCommandAndAssertOutput($params, $expectedOutputMessages);
    }

    public function testHandleCommandWithLimitLessThanBatchSize()
    {
        $totalNumberOfUsers = 4;
        $limit = 2;
        $batchSize = 3;

        $this->mockUserRepoWithLimit($totalNumberOfUsers, $limit);

        $this->mockOpenAMWithUsers();

        $params = [
            'isLiveRun' => true,
            'batchSize' => $batchSize,
            'limit' => $limit
        ];

        $expectedOutputMessages = [
            "[Batch 1] Setting last login time for user 'loginId-1' to '2020-01-01 10:00:00'",
            "[Batch 1] Setting last login time for user 'loginId-2' to '2020-01-01 11:00:00'"
        ];

        $this->runCommandAndAssertOutput($params, $expectedOutputMessages);
    }

    public function testHandleCommandInDryRunMode()
    {
        $totalNumberOfUsers = 4;
        $batchSize = 2;

        $this->mockUserRepoWithUsers($totalNumberOfUsers);

        $this->repoMap['User']->shouldReceive('saveOnFlush')->times($totalNumberOfUsers);
        $this->repoMap['User']->shouldReceive('flushAll')->never();

        $this->mockOpenAMWithUsers();

        $params = [
            'isLiveRun' => false,
            'batchSize' => $batchSize,
            'progressBar' => $this->makeProgressBar()
        ];

        $expectedOutputMessages = [
            "[Batch 1] Setting last login time for user 'loginId-1' to '2020-01-01 10:00:00'",
            "[Batch 1] Setting last login time for user 'loginId-2' to '2020-01-01 11:00:00'",
            "[Batch 1] Dry run mode. Skipping database update",
            "[Batch 2] Setting last login time for user 'loginId-3' to '2020-01-02 10:00:00'",
            "[Batch 2] Setting last login time for user 'loginId-4' to '2020-01-02 11:00:00'",
            "[Batch 2] Dry run mode. Skipping database update"
        ];

        $this->runCommandAndAssertOutput($params, $expectedOutputMessages);
    }

    /**
     * @param array $params
     * @param array $expectedMessages
     */
    protected function runCommandAndAssertOutput(array $params, array $expectedMessages): void
    {
        $result = $this->sut->handleCommand(PopulateLastLoginFromOpenAmCmd::create($params))->toArray();

        $messages = $result["messages"];

        foreach ($expectedMessages as $expectedMessage) {
            $this->assertContains($expectedMessage, $messages);
        }
    }

    /**
     * @param $count
     * @return ArrayIterator
     */
    private function iterableListOfUsers($count)
    {
        $result = [];

        for ($i=1; $i<=$count; $i++) {
            $mockUser = m::mock(\Dvsa\Olcs\Api\Entity\User\User::class);
            $mockUser->shouldReceive('getPid')
                ->andReturn('pid-' . $i);
            $mockUser->shouldReceive('getLoginId')
                ->andReturn('loginId-' . $i);
            $mockUser->shouldReceive('setLastLoginAt');

            $result[][] = $mockUser;
        }

        return new ArrayIterator($result);
    }

    private function makeProgressBar()
    {
        return new ProgressBar(new NullOutput());
    }

    /**
     * @param int $totalNumberOfUsers
     * @param int $batchSize
     */
    protected function mockUserRepoThatSavesAllUsers(int $totalNumberOfUsers, int $batchSize): void
    {
        $this->mockUserRepoWithUsers($totalNumberOfUsers);

        $this->repoMap['User']->shouldReceive('saveOnFlush')->times($totalNumberOfUsers);
        $this->repoMap['User']->shouldReceive('flushAll')->times($batchSize);
    }

    /**
     * @param int $totalNumberOfUsers
     * @param int $batchSize
     * @param int $usersSaved
     */
    protected function mockUserRepoThatSavesSomeUsers(int $totalNumberOfUsers, int $batchSize, int $usersSaved): void
    {
        $this->mockUserRepoWithUsers($totalNumberOfUsers);

        $this->repoMap['User']->shouldReceive('saveOnFlush')->times($usersSaved);
        $this->repoMap['User']->shouldReceive('flushAll')->times($batchSize);
    }

    /**
     * @param int $totalNumberOfUsers
     * @param int $batchSize
     */
    protected function mockUserRepoThatDoesNotSaveUsers(int $totalNumberOfUsers): void
    {
        $this->mockUserRepoWithUsers($totalNumberOfUsers);

        $this->repoMap['User']->shouldReceive('saveOnFlush')->never();
        $this->repoMap['User']->shouldReceive('flushAll')->never();
    }

    /**
     * @param int $totalNumberOfUsers
     * @param int $batchSize
     * @return void
     */
    protected function mockUserRepoWithUsers(int $totalNumberOfUsers) : void
    {
        $this->repoMap['User']->shouldReceive('fetchUsersCountWithoutLastLoginTime')
            ->andReturn($totalNumberOfUsers);

        $this->repoMap['User']->shouldReceive('fetchUsersWithoutLastLoginTime')
            ->andReturn($this->iterableListOfUsers($totalNumberOfUsers));

        $this->repoMap['User']->shouldReceive('clear');
    }

    /**
     * @param int $totalNumberOfUsers
     * @param int $limit
     */
    protected function mockUserRepoWithLimit(int $totalNumberOfUsers, int $limit): void
    {
        $this->repoMap['User']->shouldReceive('fetchUsersCountWithoutLastLoginTime')
            ->andReturn($totalNumberOfUsers);

        $this->repoMap['User']->shouldReceive('fetchUsersWithoutLastLoginTime')
            ->andReturn($this->iterableListOfUsers($totalNumberOfUsers));

        $this->repoMap['User']->shouldReceive('saveOnFlush')->times($limit);
        $this->repoMap['User']->shouldReceive('flushAll')->times(1);
        $this->repoMap['User']->shouldReceive('clear')->times(1);
    }

    protected function mockOpenAMWithUsers(): void
    {
        $this->mockOpenAmUserService
            ->shouldReceive('fetchUsers')
            ->with(['pid-1', 'pid-2'])
            ->andReturn(
                [
                    [
                        'pid' => 'pid-1',
                        'lastLoginTime' => '2020-01-01 10:00:00'
                    ],
                    [
                        'pid' => 'pid-2',
                        'lastLoginTime' => '2020-01-01 11:00:00'
                    ]
                ]
            );
        $this->mockOpenAmUserService
            ->shouldReceive('fetchUsers')
            ->with(['pid-3', 'pid-4'])
            ->andReturn(
                [
                    [
                        'pid' => 'pid-3',
                        'lastLoginTime' => '2020-01-02 10:00:00'
                    ],
                    [
                        'pid' => 'pid-4',
                        'lastLoginTime' => '2020-01-02 11:00:00'
                    ]
                ]
            );
    }

    protected function mockOpenAMWithMissingUsers(): void
    {
        $this->mockOpenAmUserService
            ->shouldReceive('fetchUsers')
            ->with(['pid-1', 'pid-2'])
            ->andReturn(
                [
                    [
                        'pid' => 'pid-1',
                        'lastLoginTime' => '2020-01-01 10:00:00'
                    ]
                ]
            );
        $this->mockOpenAmUserService
            ->shouldReceive('fetchUsers')
            ->with(['pid-3', 'pid-4'])
            ->andReturn(
                [
                    [
                        'pid' => 'pid-3',
                        'lastLoginTime' => '2020-01-02 10:00:00'
                    ]
                ]
            );
    }

    protected function mockOpenAMWithNoLastLoginTime(): void
    {
        $this->mockOpenAmUserService
            ->shouldReceive('fetchUsers')
            ->with(['pid-1', 'pid-2'])
            ->andReturn(
                [
                    [
                        'pid' => 'pid-1',
                        'lastLoginTime' => '2020-01-01 10:00:00'
                    ],
                    [
                        'pid' => 'pid-2'
                    ]
                ]
            );

        $this->mockOpenAmUserService
            ->shouldReceive('fetchUsers')
            ->with(['pid-3', 'pid-4'])
            ->andReturn(
                [
                    [
                        'pid' => 'pid-3',
                        'lastLoginTime' => '2020-01-01 10:00:00'
                    ],
                    [
                        'pid' => 'pid-4'
                    ]
                ]
            );
    }

    protected function mockOpenAMWithException(): void
    {
        $this->mockOpenAmUserService
            ->shouldReceive('fetchUsers')
            ->andThrow(new Exception("Exception from OpenAM"));
    }
}

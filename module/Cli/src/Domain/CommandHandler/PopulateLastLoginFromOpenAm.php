<?php

namespace Dvsa\Olcs\Cli\Domain\CommandHandler;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\OpenAmUserAwareInterface;
use Dvsa\Olcs\Api\Domain\OpenAmUserAwareTrait;
use Dvsa\Olcs\Transfer\Command\CommandInterface;

final class PopulateLastLoginFromOpenAm extends AbstractCommandHandler implements OpenAmUserAwareInterface
{
    use OpenAmUserAwareTrait;

    const DEFAULT_BATCH_SIZE = 100;

    protected $repoServiceName = 'User';

    /**
     * Handle command
     *
     * @param PopulateLastLoginFromOpenAm $command Command
     *
     * @return \Dvsa\Olcs\Api\Domain\Command\Result
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     * @throws \Exception
     */
    public function handleCommand(CommandInterface $command)
    {
        $isLiveRun = $command->isLiveRun();
        $batchSize = $this->getBatchSize($command);
        $progressBar = $command->getProgressBar();
        $numberOfUsersToProcess = $this->getNumberOfUsersToProcess($command);
        $numberOfBatches = $this->getNumberOfBatches($numberOfUsersToProcess, $batchSize);

        if ($progressBar) {
            $progressBar->start($numberOfUsersToProcess);
        }

        $totalCount = 0;
        for ($batchNumber = 1; $batchNumber <= $numberOfBatches; $batchNumber++) {
            $offset = $batchSize * ($batchNumber - 1);

            $batchedUsers = $this->getBatchedUsers($offset, $batchSize);

            if (!empty($batchedUsers)) {
                $this->result->addMessage("[Batch $batchNumber] Querying OpenAM for " . count($batchedUsers) . " users");
                $totalCount += count($batchedUsers);
                if ($progressBar) {
                    $progressBar->advance(count($batchedUsers));
                }

                try {
                    $this->processBatch($batchedUsers, $batchNumber, $isLiveRun);
                } catch (\Exception $exception) {
                    $this->result->addMessage("[Batch $batchNumber] Unable to process batch. Error : " . $exception->getMessage());
                    continue;
                }
            }
        }

        if ($progressBar) {
            $progressBar->finish();
        }

        $this->result->addMessage("Processed $totalCount users");

        return $this->result;
    }

    /**
     * Match user data with openAM result and update last login time
     *
     * @param array $openAmResult
     * @param array $users
     * @param int $batchNumber
     * @return array
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     */
    private function updateUsers(array $openAmResult, array $users, int $batchNumber): array
    {
        foreach ($openAmResult as $openAmUser) {
            $pid = $openAmUser['pid'];
            $lastLoginTime = $openAmUser['lastLoginTime'] ?? null;

            if (!empty($users[$pid])) {
                $user = $users[$pid];

                if (!empty($lastLoginTime)) {
                    $user->setLastLoginAt($lastLoginTime);
                    $this->getRepo()->saveOnFlush($user);
                    $this->result->addMessage("[Batch $batchNumber] Setting last login time for user '{$user->getLoginId()}' to '$lastLoginTime'");
                } else {
                    $this->result->addMessage("[Batch $batchNumber] No last login time found for user '{$user->getLoginId()}'");
                }

                unset($users[$pid]);
            }
        }

        return $users;
    }

    /**
     * Report users not found on OpenAM
     *
     * @param array $users
     * @param int $batchNumber
     */
    private function reportAnomalies(array $users, int $batchNumber)
    {
        foreach ($users as $user) {
            $this->result->addMessage("[Batch $batchNumber] Could not find user '{$user->getLoginId()}' in OpenAM");
        }
    }

    /**
     * @param CommandInterface $command
     * @return int
     */
    protected function getBatchSize(CommandInterface $command): int
    {
        $limit = $command->getLimit();
        $batchSize = $command->getBatchSize() ?? self::DEFAULT_BATCH_SIZE;

        if ($limit > 0 && $limit <= $batchSize) {
            $batchSize = $limit;
        }

        return $batchSize;
    }

    /**
     * @param CommandInterface $command
     * @return int
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     */
    protected function getNumberOfUsersToProcess(CommandInterface $command) : int
    {
        $limit = $command->getLimit();
        if ($limit > 0) {
            $this->result->addMessage("Limiting run to process $limit users");
            $numberOfUsersToProcess = $limit;
        } else {
            $numberOfUsersToProcess = $this->getRepo()->fetchActiveUserCount();
            $this->result->addMessage("This run will try to process $numberOfUsersToProcess users");
        }

        return $numberOfUsersToProcess;
    }

    /**
     * @param int $numberOfUsersToProcess
     * @param int $batchSize
     * @return float
     */
    protected function getNumberOfBatches(int $numberOfUsersToProcess, int $batchSize) : float
    {
        return ceil($numberOfUsersToProcess / $batchSize);
    }

    /**
     * @param int $offset
     * @param int $batchSize
     * @return array
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     */
    protected function getBatchedUsers(int $offset, int $batchSize): array
    {
        $usersToProcess = $this->getRepo()->fetchPaginatedActiveUsers($offset, $batchSize);

        $batchedUsers = [];
        foreach ($usersToProcess as $user) {
            $batchedUsers[$user->getPid()] = $user;
        }

        return $batchedUsers;
    }

    /**
     * @param array $batchedUsers
     * @param int $batchNumber
     * @param bool $isLiveRun
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     * @throws \Dvsa\Olcs\Api\Service\OpenAm\FailedRequestException
     */
    protected function processBatch(array $batchedUsers, int $batchNumber, bool $isLiveRun): void
    {
        $openAmResult = $this->getOpenAmUser()->fetchUsers(array_keys($batchedUsers));

        $unprocessedUsers = $this->updateUsers($openAmResult, $batchedUsers, $batchNumber);

        if (!empty($unprocessedUsers)) {
            $this->reportAnomalies($unprocessedUsers, $batchNumber);
        }

        if ($isLiveRun) {
            $this->result->addMessage("[Batch $batchNumber] Sending updates to database");
            $this->getRepo()->flushAll();
        } else {
            $this->result->addMessage("[Batch $batchNumber] Dry run mode. Skipping database update");
        }

        $this->result->addMessage("[Batch $batchNumber] Update complete");
    }
}

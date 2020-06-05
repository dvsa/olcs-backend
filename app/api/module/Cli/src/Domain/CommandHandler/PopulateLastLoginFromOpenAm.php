<?php

namespace Dvsa\Olcs\Cli\Domain\CommandHandler;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\Exception\RuntimeException;
use Dvsa\Olcs\Api\Domain\OpenAmUserAwareInterface;
use Dvsa\Olcs\Api\Domain\OpenAmUserAwareTrait;
use Dvsa\Olcs\Api\Entity\User\User;
use Dvsa\Olcs\Api\Service\OpenAm\FailedRequestException;
use Dvsa\Olcs\Transfer\Command\CommandInterface;

final class PopulateLastLoginFromOpenAm extends AbstractCommandHandler implements OpenAmUserAwareInterface
{
    use OpenAmUserAwareTrait;

    const DEFAULT_BATCH_SIZE = 50;

    protected $repoServiceName = 'User';

    /**
     * Handle command
     *
     * @param \Dvsa\Olcs\Cli\Domain\Command\PopulateLastLoginFromOpenAm $command Command
     *
     * @return \Dvsa\Olcs\Api\Domain\Command\Result
     * @throws RuntimeException
     */
    public function handleCommand(CommandInterface $command)
    {
        $isLiveRun = $command->isLiveRun();
        $batchSize = $this->getBatchSize($command);
        $limit = $command->getLimit();
        $progressBar = $command->getProgressBar();

        $iterableResult = $this->getRepo()->fetchUsersWithoutLastLoginTime();
        $numberOfUsersToProcess = $this->getNumberOfUsersToProcess($command);

        if ($progressBar) {
            $progressBar->start($numberOfUsersToProcess);
        }

        $batchNumber = 0;
        $batchedUsers = [];
        $totalCount = 0;
        foreach ($iterableResult as $row) {
            $user = $row[0];
            $batchedUsers[$user->getPid()] = $user;
            $totalCount++;

            if (($totalCount % $batchSize == 0) || ($totalCount == $numberOfUsersToProcess)) {
                $batchNumber++;

                if ($progressBar) {
                    $progressBar->advance(count($batchedUsers));
                }

                try {
                    $this->processBatch($batchedUsers, $batchNumber, $isLiveRun);
                } catch (\Exception $exception) {
                    $this->result->addMessage("[Batch $batchNumber] Unable to process batch. Error : " . $exception->getMessage());
                    $this->result->addMessage("[Batch $batchNumber] Users not processed : " . $this->getLoginIds($batchedUsers));
                    continue;
                } finally {
                    $batchedUsers = [];
                }
            }

            //If there's a custom limit set, end the for loop
            if ($totalCount === $limit) {
                break;
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
     * @throws RuntimeException
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
     * @throws RuntimeException
     */
    protected function getNumberOfUsersToProcess(CommandInterface $command) : int
    {
        $numberOfUsersToProcess = $this->getRepo()->fetchUsersCountWithoutLastLoginTime();
        $limit = $command->getLimit();
        if ($limit > 0 && $limit < $numberOfUsersToProcess) {
            $this->result->addMessage("Limiting run to process $limit users");
            $numberOfUsersToProcess = $limit;
        } else {
            $this->result->addMessage("This run will try to process $numberOfUsersToProcess users");
        }

        return $numberOfUsersToProcess;
    }

    /**
     * @param array $batchedUsers
     * @param int $batchNumber
     * @param bool $isLiveRun
     * @throws RuntimeException
     * @throws FailedRequestException
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

        $this->getRepo()->clear();

        $this->result->addMessage("[Batch $batchNumber] Update complete");
    }

    /**
     * @param User[] $batchedUsers
     * @return string
     */
    private function getLoginIds(array $batchedUsers)
    {
        $loginIds = [];
        foreach ($batchedUsers as $user) {
            $loginIds[] = $user->getLoginId();
        }

        return json_encode($loginIds, JSON_PRETTY_PRINT);
    }
}

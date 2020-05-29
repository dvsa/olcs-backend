<?php

namespace Dvsa\Olcs\Cli\Domain\CommandHandler;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\OpenAmUserAwareInterface;
use Dvsa\Olcs\Api\Domain\OpenAmUserAwareTrait;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Symfony\Component\Console\Helper\ProgressBar;

final class PopulateLastLoginFromOpenAm extends AbstractCommandHandler implements OpenAmUserAwareInterface
{
    use OpenAmUserAwareTrait;

    const DEFAULT_BATCH_SIZE = 10;

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
        $liveRun = $command->isLiveRun();
        $limit = $command->getLimit();
        $batchSize = $command->getBatchSize() ?? self::DEFAULT_BATCH_SIZE;

        /** @var ProgressBar $progressBar */
        $progressBar = $command->getProgressBar();

        if ($limit > 0) {
            $this->result->addMessage("Limiting run to process $limit users");
            $numberOfUsersToProcess = $limit;
            if ($limit <= $batchSize) {
                $batchSize = $limit;
            }
        } else {
            $numberOfUsersToProcess = $this->getRepo()->fetchActiveUserCount();
            $this->result->addMessage("This run will try to process $numberOfUsersToProcess users");
        }

        if ($progressBar) {
            $progressBar->start($numberOfUsersToProcess);
        }

        $numberOfBatches = ceil($numberOfUsersToProcess / $batchSize);

        $totalCount = 0;
        for ($batch = 1; $batch <= $numberOfBatches; $batch++) {
            $offset = $batchSize * ($batch - 1);

            $usersToProcess = $this->getRepo()->fetchPaginatedActiveUsers($offset, $batchSize);

            $batchedUsers = [];
            foreach ($usersToProcess as $user) {

                $batchedUsers[$user->getPid()] = $user;
            }

            if (!empty($batchedUsers)) {
                $this->result->addMessage("[Batch $batch] Querying OpenAM for " . count($batchedUsers) . " users");
                $totalCount += count($batchedUsers);
                if ($progressBar) {
                    $progressBar->advance(count($batchedUsers));
                }

                try {
                    $openAmResult = $this->getOpenAmUser()->fetchUsers(array_keys($batchedUsers));
                } catch (\Exception $exception) {
                    $this->result->addMessage("[Batch $batch] Unable to get OpenAM data. Error : " . $exception->getMessage());
                    continue;
                }

                $batchedUsers = $this->updateUsers($openAmResult, $batchedUsers, $batch);

                if (!empty($batchedUsers)) {
                    $this->reportAnomalies($batchedUsers, $batch);
                }

                if ($liveRun) {
                    $this->result->addMessage("[Batch $batch] Sending updates to database");
                    $this->getRepo()->flushAll();
                } else {
                    $this->result->addMessage("[Batch $batch] Dry run mode. Skipping database update");
                }

                $this->result->addMessage("[Batch $batch] Update complete");
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
     * @param int $batch
     * @return array
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     */
    private function updateUsers(array $openAmResult, array $users, int $batch): array
    {
        foreach ($openAmResult as $openAmUser) {
            $pid = $openAmUser['pid'];
            $lastLoginTime = $openAmUser['lastLoginTime'] ?? null;

            if (!empty($users[$pid])) {
                $user = $users[$pid];

                if (!empty($lastLoginTime)) {
                    $user->setLastLoginAt($lastLoginTime);
                    $this->getRepo()->saveOnFlush($user);
                    $this->result->addMessage("[Batch $batch] Setting last login time for user '{$user->getLoginId()}' to '$lastLoginTime'");
                } else {
                    $this->result->addMessage("[Batch $batch] No last login time found for user '{$user->getLoginId()}'");
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
     * @param int $batch
     */
    private function reportAnomalies(array $users, int $batch)
    {
        foreach ($users as $user) {
            $this->result->addMessage("[Batch $batch] Could not find user '{$user->getLoginId()}' in OpenAM");
        }
    }
}

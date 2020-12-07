<?php

namespace Dvsa\Olcs\Cli\Domain\CommandHandler;

use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareTrait;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\Exception\RuntimeException;
use Dvsa\Olcs\Api\Domain\OpenAmUserAwareInterface;
use Dvsa\Olcs\Api\Domain\OpenAmUserAwareTrait;
use Dvsa\Olcs\Api\Entity\User\User;
use Dvsa\Olcs\Api\Service\OpenAm\FailedRequestException;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Laminas\Console\Adapter\AdapterInterface as ConsoleAdapter;

final class PopulateLastLoginFromOpenAm extends AbstractCommandHandler implements OpenAmUserAwareInterface, AuthAwareInterface
{
    use OpenAmUserAwareTrait;
    use AuthAwareTrait;

    const DEFAULT_BATCH_SIZE = 50;

    protected $repoServiceName = 'User';

    /**
     * @var bool
     */
    protected $isLiveRun = false;

    /**
     * @var ConsoleAdapter
     */
    protected $console;

    /**
     * @var User
     */
    protected $systemUser;

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
        $this->isLiveRun = $command->isLiveRun();
        $batchSize = $this->getBatchSize($command);
        $limit = $command->getLimit();
        $progressBar = $command->getProgressBar();
        $this->console = $command->getConsole();
        $this->systemUser = $this->getSystemUser();
        $totalCount = 0;
        $batchNumber = 0;
        $batchedUsers = [];

        $iterableResult = $this->getRepo()->fetchUsersWithoutLastLoginTime();
        $numberOfUsersToProcess = $this->getNumberOfUsersToProcess($command);

        $this->output("");

        if ($progressBar) {
            $progressBar->start($numberOfUsersToProcess);
        }

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
                    $this->processBatch($batchedUsers, $batchNumber);
                } catch (\Exception $exception) {
                    $this->output("[Batch $batchNumber] Unable to process batch. Error : " . $exception->getMessage());
                    $this->output("[Batch $batchNumber] Users not processed : " . $this->getLoginIds($batchedUsers));
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

        $this->output("");
        $this->output("Processed $totalCount users");

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
                    $this->output("[Batch $batchNumber] Setting last login time for user '{$user->getLoginId()}' to '$lastLoginTime'");

                    if ($this->isLiveRun) {
                        $this->getRepo()->updateLastLogin($user, new \DateTime($lastLoginTime), $this->systemUser);
                    } else {
                        $this->output("[Batch $batchNumber] Dry run mode. Skipping database update");
                    }
                } else {
                    $this->output("[Batch $batchNumber] No last login time found for user '{$user->getLoginId()}'");
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
     * @throws \Exception
     */
    private function reportAnomalies(array $users, int $batchNumber)
    {
        foreach ($users as $user) {
            $this->output("[Batch $batchNumber] Could not find user '{$user->getLoginId()}' in OpenAM");
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
            $this->output("Limiting run to process $limit users");
            $numberOfUsersToProcess = $limit;
        } else {
            $this->output("This run will try to process $numberOfUsersToProcess users");
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
    protected function processBatch(array $batchedUsers, int $batchNumber): void
    {
        $this->output("");
        $openAmResult = $this->getOpenAmUser()->fetchUsers(array_keys($batchedUsers));

        $unprocessedUsers = $this->updateUsers($openAmResult, $batchedUsers, $batchNumber);

        if (!empty($unprocessedUsers)) {
            $this->reportAnomalies($unprocessedUsers, $batchNumber);
        }

        $this->getRepo()->clear();

        $this->output("[Batch $batchNumber] Update complete");
        $this->output("");
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

    /**
     * @param $message
     * @throws \Exception
     */
    protected function output($message)
    {
        if ($this->console) {
            if ($message != "") {
                $message = (new \DateTime())->format(\DateTime::W3C) . ' ' . $message;
            }

            $this->console->writeLine($message);
        }
    }
}

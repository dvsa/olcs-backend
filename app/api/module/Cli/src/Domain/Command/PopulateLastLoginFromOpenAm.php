<?php

namespace Dvsa\Olcs\Cli\Domain\Command;

use Dvsa\Olcs\Transfer\Command\AbstractCommand;
use Symfony\Component\Console\Helper\ProgressBar;
use Laminas\Console\Adapter\AdapterInterface as ConsoleAdapter;

/**
 * Class ImportUsersFromCsv
 * @package Dvsa\Olcs\Cli\Domain\Command
 */
final class PopulateLastLoginFromOpenAm extends AbstractCommand
{
    /**
     * @var bool
     */
    protected $isLiveRun = false;

    /**
     * @var int
     */
    protected $limit;

    /**
     * @var int
     */
    protected $batchSize;

    /**
     * @var ProgressBar
     */
    protected $progressBar;

    /**
     * @var ConsoleAdapter
     */
    protected $console;

    /**
     * @return bool
     */
    public function isLiveRun(): bool
    {
        return $this->isLiveRun;
    }

    /**
     * @return int
     */
    public function getLimit(): ?int
    {
        return $this->limit;
    }

    /**
     * @return int
     */
    public function getBatchSize(): ?int
    {
        return $this->batchSize;
    }

    /**
     * @return ProgressBar
     */
    public function getProgressBar() : ?ProgressBar
    {
        return $this->progressBar;
    }

    /**
     * @return ConsoleAdapter
     */
    public function getConsole()
    {
        return $this->console;
    }
}

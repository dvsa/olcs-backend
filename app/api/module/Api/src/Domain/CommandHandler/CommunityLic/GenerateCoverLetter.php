<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\CommunityLic;

use Dvsa\Olcs\Api\Domain\Command\CommunityLic\GenerateCoverLetter as GenerateCoverLetterCmd;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;

/**
 * Generate Cover Letter
 */
final class GenerateCoverLetter extends AbstractCommandHandler
{
    protected $repoServiceName = 'Licence';

    /**
     * Handle Command
     *
     * @param GenerateCoverLetterCmd $command Command
     *
     * @return Result
     */
    public function handleCommand(CommandInterface $command)
    {
        return $this->result;
    }
}

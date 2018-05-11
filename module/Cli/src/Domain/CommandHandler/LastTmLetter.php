<?php

namespace Dvsa\Olcs\Cli\Domain\CommandHandler;

use Dvsa\Olcs\Api\Domain\Repository\Licence;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;


/**
 * Export data to csv files for data.gov.uk
 *
 * @author Dmitry Golubev <dmitrij.golubev@valtech.co.uk>
 */
final class LastTmLetter extends AbstractCommandHandler
{
    /**
     * @var string
     */
    protected $repoServiceName = 'Licence';

    /**
     * Handle command
     *
     * @param \Dvsa\Olcs\Cli\Domain\Command\DataGovUkExport $command Command
     *
     * @return \Dvsa\Olcs\Api\Domain\Command\Result
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     * @throws \Exception
     */
    public function handleCommand(CommandInterface $command)
    {
        /** @var Licence $licenceRepo */
        $licenceRepo = $this->getRepo();
        $eligibleLicences = $licenceRepo->fetchForLastTmAutoLetter();
        echo "\n";
        print_r($eligibleLicences);
        echo "\n";
        $this->result->addMessage('Sample message ');
        return $this->result;
    }
}

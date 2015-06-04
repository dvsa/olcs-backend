<?php

/**
 * Update Financial Evidence
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Application;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Entity\Application\Application;
use Dvsa\Olcs\Transfer\Command\Application\UpdateFinancialEvidence as Cmd;

/**
 * Update Financial Evidence
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
final class UpdateFinancialEvidence extends AbstractCommandHandler
{
    protected $repoServiceName = 'Application';

    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();

        /** @var Application $application */
        $application = $this->getRepo()->fetchUsingId($command, Query::HYDRATE_OBJECT, $command->getVersion());

        $application->updateFinancialEvidence($command->getFinancialEvidenceUploaded());

        $this->getRepo()->save($application);
        $result->addMessage('Financial evidence section has been updated');
        return $result;
    }
}

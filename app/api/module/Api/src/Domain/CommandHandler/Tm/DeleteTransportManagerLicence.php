<?php

/**
 * DeleteTransportManagerLicence.php
 *
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Tm;

use Doctrine\Common\Collections\Criteria;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\Command\Result;

/**
 * Delete Transport Manager Licence
 *
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 */
final class DeleteTransportManagerLicence extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'TransportManagerLicence';

    public function handleCommand(CommandInterface $command)
    {
        $transportManagers = $this->getRepo()->fetchForLicence($command->getLicence());

        foreach ($transportManagers as $transportManager) {
            $this->getRepo()->delete($transportManager);
        }

        $result = new Result();
        $result->addMessage('Removed transport managers for licence.');

        return $result;
    }
}

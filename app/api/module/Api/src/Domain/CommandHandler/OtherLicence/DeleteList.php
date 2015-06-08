<?php

/**
 * Delete a list of Other Licences
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\OtherLicence;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Entity\OtherLicence\OtherLicence;
use Dvsa\Olcs\Transfer\Command\CommandInterface;

/**
 * Delete a list of Other Licences
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
final class DeleteList extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'OtherLicence';

    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();

        foreach ($command->getIds() as $olId) {
            /* @var $otherLicence OtherLicence */
            $otherLicence = $this->getRepo()->fetchById($olId);
            $this->getRepo()->delete($otherLicence);
            $result->addMessage("Other Licence ID {$olId} deleted");
        }

        return $result;
    }
}

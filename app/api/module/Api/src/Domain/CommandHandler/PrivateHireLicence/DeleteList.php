<?php

/**
 * Delete a list of PrivateHireLicence
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\PrivateHireLicence;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Transfer\Command\CommandInterface;

/**
 * Delete a list of PrivateHireLicence
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
final class DeleteList extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'PrivateHireLicence';
    protected $extraRepos = ['Licence'];

    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();

        foreach ($command->getIds() as $tmeId) {
            /* @var $phl \Dvsa\Olcs\Api\Entity\Licence\PrivateHireLicence */
            $phl = $this->getRepo()->fetchById($tmeId);
            $this->getRepo()->delete($phl);
            $result->addMessage("PrivateHireLicence ID {$tmeId} deleted");
        }

        return $result;
    }
}

<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Vehicle;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Transfer\Command\CommandInterface;

/**
 * Update a Vehicle Section 26 status
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
final class UpdateSection26 extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'Vehicle';

    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();

        foreach ($command->getIds() as $id) {
            /* @var $vehicle \Dvsa\Olcs\Api\Entity\Vehicle\Vehicle */
            $vehicle = $this->getRepo()->fetchById($id);
            $vehicle->setSection26($command->getSection26() === 'Y');
            $this->getRepo()->save($vehicle);
        }

        $result->addMessage(sprintf('Updated Section26 on %d Vehicle(s).', count($command->getIds())));

        return $result;
    }
}

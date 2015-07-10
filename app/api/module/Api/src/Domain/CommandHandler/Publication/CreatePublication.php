<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Publication;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Transfer\Command\CommandInterface;

class CreatePublication extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'Publication';

    /**
     * @param CommandInterface $command
     * @return \Dvsa\Olcs\Api\Domain\Command\Result
     */
    public function handleCommand(CommandInterface $command)
    {
        /**
         * logix:
         * Obtain correct publication from repository
         * Spawn a new publication link
         * Fill it with correct data
         * Save
         */

        if ($command instanceof Multiple) {
            $result = new Result();
            foreach ($command->getTrafficAreas() as $trafficArea) {
                foreach ($command->getTypes() as $type) {
                    $result->merge($this->createPublication($trafficArea, $type, $command));
                }
            }
        } else {
            $result = $this->createPublication($command->getTrafficArea(), $command->getType(), $command);
        }

        return $result;
    }

    /**
     * @param CommandInterface $command
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     */
    private function createPublication($trafficArea, $type, CommandInterface $command)
    {
        $publication = $this->getRepo()->fetchLatestForTrafficAreaAndType($trafficArea, $type);

        $publicationLink = $publication->newLink();

        $this->publicationService->createPublication($publicationLink, $command->getArrayCopy());

        $this->getRepo()->save($publication);

        $result = new Result();
        $result->addId('publication'. $publicationLink->getId(), $publicationLink->getId());
        $result->addMessage('Publication link Created');

        return $result;
    }
}
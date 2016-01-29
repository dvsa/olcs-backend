<?php

/**
 * Publish a publication
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Publication;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Transfer\Command\Publication\Publish as PublishCommand;
use Dvsa\Olcs\Api\Entity\Publication\Publication as PublicationEntity;

/**
 * Publish a publication
 */
final class Publish extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'Publication';

    /**
     * @param CommandInterface $command
     * @return Result
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     */
    public function handleCommand(CommandInterface $command)
    {
        /**
         * @var PublicationEntity $publication
         * @var PublishCommand $command
         */
        $publication = $this->getRepo()->fetchUsingId($command);
        $refData = $this->getRepo()->getRefdataReference(PublicationEntity::PUB_PRINTED_STATUS);
        $publication->publish($refData);
        $this->getRepo()->save($publication);

        $result = new Result();
        $result->addId('Publication', $publication->getId());
        $result->addMessage('Publication was published');

        return $result;
    }
}

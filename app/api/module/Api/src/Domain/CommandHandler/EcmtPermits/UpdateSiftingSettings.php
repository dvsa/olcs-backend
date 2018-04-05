<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\EcmtPermits;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Dvsa\Olcs\Transfer\Command;

/**
 * Handler for UPDATE Sifting Settings
 *
 * @author Henry White <henry.white@capgemini.com>
 */
class UpdateSiftingSettings extends AbstractCommandHandler
{
    protected $repoServiceName = 'SiftingSettings';

    /**
     * @param Command\EcmtPermits\UpdateSiftingSettings $command
     *
     * @return \Dvsa\Olcs\Api\Domain\Command\Result
     */
    public function handleCommand(Command\CommandInterface $command)
    {
        /** @var \Dvsa\Olcs\Api\Entity\EcmtSiftingSettings $entity */
        $entity = $this->getRepo()->fetchUsingId($command);

        $entity->update(
            new DateTime($command->getStartDate()),
            new DateTime($command->getEndDate()),
            $command->getTotalQuotaPermits()
        );

        $this->getRepo()->save($entity);

        return $this->result->addMessage("Sifting settings updated");
    }
}

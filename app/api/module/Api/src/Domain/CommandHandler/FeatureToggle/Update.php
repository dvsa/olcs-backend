<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\FeatureToggle;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle as ToggleEntity;
use Dvsa\Olcs\Transfer\Command\FeatureToggle\Update as UpdateToggleCmd;

/**
 * Update a FeatureToggle
 *
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
final class Update extends AbstractCommandHandler
{
    protected $repoServiceName = 'FeatureToggle';

    public function handleCommand(CommandInterface $command): Result
    {
        /**
         * @var UpdateToggleCmd $command
         * @var ToggleEntity $toggle
         */
        $toggle = $this->getRepo()->fetchUsingId($command);
        $toggle->update(
            $command->getConfigName(),
            $command->getFriendlyName(),
            $this->refData($command->getStatus())
        );

        $this->getRepo()->save($toggle);

        $this->result->addId('FeatureToggle', $toggle->getId());
        $this->result->addMessage("Feature toggle '{$toggle->getId()}' updated");
        return $this->result;
    }
}

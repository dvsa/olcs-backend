<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\IrhpPermitApplication;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\Exception\NotFoundException;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;
use Dvsa\Olcs\Transfer\Command\CommandInterface;

class Delete extends AbstractCommandHandler implements ToggleRequiredInterface
{
    use ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::ADMIN_PERMITS];
    protected $repoServiceName = 'IrhpPermitApplication';

    /**
     * @param \Dvsa\Olcs\Transfer\Command\CommandInterface $command
     * @return \Dvsa\Olcs\Api\Domain\Command\Result
     * @throws \Dvsa\Olcs\Api\Domain\Exception\RuntimeException
     */
    public function handleCommand(CommandInterface $command)
    {
        $id = $command->getId();
        $irhpPermitApplication = $this->getRepo()->fetchById($id);

        try {
            $this->getRepo()->delete($irhpPermitApplication);
            $this->result->addId('id', $id);
            $this->result->addMessage(sprintf('IRHP Permit Application deleted', $id));
        } catch (NotFoundException $e) {
            $this->result->addMessage(sprintf('Id %d not found', $id));
        }

        return $this->result;
    }
}
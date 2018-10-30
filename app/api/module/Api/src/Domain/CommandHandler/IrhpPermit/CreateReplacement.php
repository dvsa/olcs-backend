<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\IrhpPermit;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermit as IrhpPermitEntity;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;

/**
 * Create an IRHP Permit
 *
 * @author Andy Newton <andy@vitri.ltd>
 */
final class CreateReplacement extends AbstractCommandHandler
{
    use ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::ADMIN_PERMITS];
    protected $repoServiceName = 'IrhpPermit';
    protected $extraRepos = ['IrhpPermitRange'];

    /**
     * @param CommandInterface $command
     * @return Result
     * @throws ValidationException
     */
    public function handleCommand(CommandInterface $command): Result
    {
        $oldPermit = $this->getRepo()->fetchById($command->getReplaces());
        $newRange = $this->getRepo('IrhpPermitRange')->fetchById($command->getIrhpPermitRange());
        $status = $this->refData(IrhpPermitEntity::STATUS_PENDING);

        $irhpPermit = IrhpPermitEntity::createReplacement(
            $oldPermit,
            $newRange,
            $status,
            $command->getPermitNumber()
        );

        try {
            $this->getRepo()->save($irhpPermit);
        } catch (\Exception $e) {
            throw new ValidationException(['An error occurred saving the replacement permit']);
        }

        $this->result->addId('IrhpPermit', $irhpPermit->getId());
        $this->result->addMessage("Permit {$command->getPermitNumber()} Created");

        return $this->result;
    }
}

<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\IrhpPermitWindow;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Api\Domain\ToggleRequiredInterface;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitWindow as WindowEntity;
use Dvsa\Olcs\Transfer\Command\IrhpPermitWindow\Create as CreateWindowCmd;

use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;

/**
 * Create an IRHP Permit Window
 *
 * @author Andy Newton
 */
final class Create extends AbstractCommandHandler implements ToggleRequiredInterface
{
    use IrhpPermitWindowOverlapTrait;
    use ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::ADMIN_PERMITS];

    protected $repoServiceName = 'IrhpPermitWindow';
    protected $extraRepos = ['IrhpPermitStock'];

    /**
     * @param CommandInterface $command
     * @return Result
     * @throws ValidationException
     */
    public function handleCommand(CommandInterface $command): Result
    {
        // If there are overlapping windows.
        if ($this->numberOfOverlappingWindows($command->getIrhpPermitStock(), $command->getStartDate(), $command->getEndDate()) > 0) {
            throw new ValidationException(['The dates overlap with another window for this Permit stock']);
        }

        // If the window starts in the past.
        $today = (new DateTime())->format('Y-m-d');
        $start = (new DateTime($command->getStartDate()))->format('Y-m-d');
        if ($today > $start) {
            throw new ValidationException(['You cannot create a window that starts in the past']);
        }

        $irhpPermitStock = $this->getRepo('IrhpPermitStock')->fetchById($command->getIrhpPermitStock());
        /**
         * @var CreateWindowCmd $command
         */
        $window = WindowEntity::create(
            $irhpPermitStock,
            $command->getStartDate(),
            $command->getEndDate(),
            $command->getDaysForPayment()
        );

        $this->getRepo()->save($window);

        $this->result->addId('IrhpPermitWindow', $window->getId());
        $this->result->addMessage("IRHP Permit Window '{$window->getId()}' created");

        return $this->result;
    }
}

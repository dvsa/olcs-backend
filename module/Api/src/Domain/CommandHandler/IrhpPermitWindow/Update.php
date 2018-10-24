<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\IrhpPermitWindow;

use DateTime;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitWindow as WindowEntity;
use Dvsa\Olcs\Transfer\Command\IrhpPermitWindow\Update as UpdateWindowCmd;
use Dvsa\Olcs\Api\Domain\ToggleAwareTrait;
use Dvsa\Olcs\Api\Entity\System\FeatureToggle;

/**
 * Update an IRHP Permit Window
 *
 * @author Andy Newton
 */
final class Update extends AbstractCommandHandler
{
    use IrhpPermitWindowOverlapTrait;
    use ToggleAwareTrait;

    protected $toggleConfig = [FeatureToggle::ADMIN_PERMITS];

    protected $repoServiceName = 'IrhpPermitWindow';
    protected $extraRepos = ['IrhpPermitStock'];

    public function handleCommand(CommandInterface $command): Result
    {
        /**
         * @var UpdateWindowCmd $command
         * @var WindowEntity $window
         */

        $window = $this->getRepo()->fetchById($command->getId());

        if ($window->hasEnded()) {
            throw new ValidationException(['Windows which have ended cannot be edited']);
        }

        if ($window->isActive()) {
            $windowStart = new DateTime($window->getStartDate());
            $editStart = new DateTime($command->getStartDate());

            if ($windowStart->format('Y-m-d') !== $editStart->format('Y-m-d')) {
                throw new ValidationException(['It is not permitted to edit the start date of an Active Window']);
            }

            $today = (new DateTime())->format('Y-m-d');
            $editEnd = (new DateTime($command->getEndDate()))->format('Y-m-d');

            if ($editEnd < $today) {
                throw new ValidationException([
                    'The end date of an Active Window must be greater than or equal to todays date'
                ]);
            }
        } else {
            // Shouldn't be able to edit a future windows start date to before todays date
            $today = (new DateTime())->format('Y-m-d');
            $editStart = (new DateTime($command->getStartDate()))->format('Y-m-d');

            if ($today > $editStart) {
                throw new ValidationException(['The Start date must be greater than or equal to Todays date']);
            }
        }

        if ($this->numberOfOverlappingWindows($command->getIrhpPermitStock(), $command->getStartDate(), $command->getEndDate(), $command->getId()) === 0) {
            $permitStock = $this->getRepo('IrhpPermitStock')->fetchById($command->getIrhpPermitStock());

            $window->update(
                $permitStock,
                $command->getStartDate(),
                $command->getEndDate(),
                $command->getDaysForPayment()
            );

            $this->getRepo()->save($window);

            $this->result->addId('Irhp Permit Window', $window->getId());
            $this->result->addMessage("Irhp Permit Window '{$window->getId()}' Updated");
        } else {
            throw new ValidationException(['The dates overlap with another window for this Permit stock']);
        }

        return $this->result;
    }
}

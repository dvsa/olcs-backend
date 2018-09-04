<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\IrhpPermitWindow;

use DateTime;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitWindow as WindowEntity;
use Dvsa\Olcs\Transfer\Command\IrhpPermitWindow\Update as UpdateWindowCmd;

/**
 * Update an IRHP Permit Window
 *
 * @author Andy Newton
 */
final class Update extends AbstractCommandHandler
{
    use IrhpPermitWindowOverlapTrait;

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
            $this->result->addMessage("Irhp Permit Window '{$window->getId()}' updated");
        } else {
            throw new ValidationException(['The dates overlap with another window for this Permit stock']);
        }

        return $this->result;
    }
}

<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\IrhpPermitWindow;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Entity\Permits\IrhpPermitWindow as WindowEntity;
use Dvsa\Olcs\Transfer\Command\IrhpPermitWindow\Create as CreateWindowCmd;

/**
 * Create an IRHP Permit Window
 *
 * @author Andy Newton
 */
final class Create extends AbstractCommandHandler
{
    use IrhpPermitWindowOverlapTrait;

    protected $repoServiceName = 'IrhpPermitWindow';
    protected $extraRepos = ['IrhpPermitStock'];

    /**
     * @param CommandInterface $command
     * @return Result
     * @throws ValidationException
     */
    public function handleCommand(CommandInterface $command): Result
    {
        if ($this->overlapsExistingWindow($command->getIrhpPermitStock(), $command->getStartDate(), $command->getEndDate()) > 0) {
            throw new ValidationException(['The dates overlap with another window for this Permit stock']);
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

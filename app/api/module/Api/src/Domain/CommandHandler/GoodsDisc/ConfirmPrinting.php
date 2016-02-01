<?php

/**
 * Confirm goods discs printing
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\GoodsDisc;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;

/**
 * Confirm goods discs printing
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
final class ConfirmPrinting extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'GoodsDisc';

    protected $extraRepos = ['DiscSequence'];

    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();

        $discs = $this->getRepo()->fetchDiscsToPrint(
            $command->getNiFlag(), $command->getLicenceType()
        );
        $discIds = array_column($discs, 'id');
        if ($command->getIsSuccessfull()) {
            $this->getRepo()->setIsPrintingOffAndAssignNumbers($discIds, $command->getStartNumber());
            $this->setNewStartNumber(
                $command->getLicenceType(), $command->getDiscSequence(), $command->getEndNumber() + 1
            );
            $result->addMessage('Printing flag is now off and numbers assigned to discs');
        } else {
            $this->getRepo()->setIsPrintingOff($discIds);
            $result->addMessage('Printing flag is now off');
        }

        return $result;
    }

    protected function setNewStartNumber($licenceType, $discSequence, $newStartNumber)
    {
        $entity = $this->getRepo('DiscSequence')->fetchById($discSequence);
        $entity->setDiscStartNumber($licenceType, $newStartNumber);
        $this->getRepo('DiscSequence')->save($entity);
    }
}

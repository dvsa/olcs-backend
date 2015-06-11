<?php

/**
 * Variation Update Addresses
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Variation;

use Dvsa\Olcs\Api\Domain\Command\Application\UpdateAddresses as ApplicationUpdateAddresses;
use Dvsa\Olcs\Api\Domain\AuthAwareInterface;
use Dvsa\Olcs\Api\Domain\AuthAwareTrait;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\Command\Task\CreateTask;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Entity\System\Category;
use Dvsa\Olcs\Api\Entity\User\Permission;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Doctrine\ORM\Query;

/**
 * Variation Update Addresses
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
final class UpdateAddresses extends AbstractCommandHandler implements AuthAwareInterface, TransactionedInterface
{
    use AuthAwareTrait;

    private $result;

    public function handleCommand(CommandInterface $command)
    {
        $result = $this->getCommandHandler()->handleCommand(
            UpdateApplicationAddresses::create($command->getArrayCopy())
        );

        // @NOTE: duped with Licence\UpdateAddresses
        if ($result->getFlag('isDirty') && $this->isGranted(Permission::SELFSERVE_USER)) {
            $taskParams = [
                'category' => Category::CATEGORY_APPLICATION,
                'subCategory' => Category::TASK_SUB_CATEGORY_APPLICATION_ADDRESS_CHANGE_DIGITAL,
                'description' => 'Address Change',
                'licence' => $command->getId()
            ];

            $result->merge($this->getCommandHandler()->handleCommand(CreateTask::create($taskParams)));
        }

        $result->setFlag('hasChanged', $result->getFlag('isDirty'));

        return $result;
    }
}

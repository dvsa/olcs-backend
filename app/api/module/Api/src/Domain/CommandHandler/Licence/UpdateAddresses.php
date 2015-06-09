<?php

/**
 * Licence Update Addresses
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Licence;

use Dvsa\Olcs\Api\Domain\Command\Licence\SaveAddresses;
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
 * Licence Update Addresses
 *
 * @author Nick Payne <nick.payne@valtech.co.uk>
 */
final class UpdateAddresses extends AbstractCommandHandler implements AuthAwareInterface, TransactionedInterface
{
    use AuthAwareTrait;

    private $isDirty = false;
    private $result;

    public function handleCommand(CommandInterface $command)
    {
        $this->result = new Result();
        $this->result->merge(
            $this->getCommandHandler()->handleCommand(
                SaveAddresses::create($command->getArrayCopy())
            )
        );

        if ($this->isDirty && $this->isGranted(Permission::SELFSERVE_USER)) {
            $taskParams = [
                'category' => Category::CATEGORY_APPLICATION,
                'subCategory' => Category::TASK_SUB_CATEGORY_APPLICATION_ADDRESS_CHANGE_DIGITAL,
                'description' => 'Address Change',
                'licence' => $licence->getId()
            ];

            $this->result->merge($this->getCommandHandler()->handleCommand(CreateTask::create($taskParams)));
        }

        $this->result->setFlag('hasChanged', $this->isDirty);

        return $this->result;
    }
}

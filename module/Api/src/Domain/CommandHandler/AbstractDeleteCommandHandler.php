<?php

/**
 * Delete Abstract
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\Exception\NotFoundException;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Entity as Entities;
use Doctrine\ORM\Query;

/**
 * Delete Abstract
 */
abstract class AbstractDeleteCommandHandler extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName;

    /**
     * Delete Command Handler Abstract
     *
     * @param CommandInterface $command
     * @return Result
     */
    public function handleCommand(CommandInterface $command)
    {
        // would be better as an interface check but BC...
        if (method_exists($command, 'getIds')) {
            $ids = $command->getIds();
        } elseif (method_exists($command, 'getId')) {
            $ids = [$command->getId()];
        } else {
            $ids = [];
        }

        return $this->doDelete($ids);
    }

    protected function doDelete(array $ids)
    {
        $result = new Result();
        foreach ($ids as $id) {
            try {
                $this->getRepo()->delete(
                    $this->getRepo()->fetchById($id)
                );

                $result->addId('id' . $id, $id);
                $result->addMessage(sprintf('Id %d deleted', $id));
            } catch (NotFoundException $e) {
                $result->addMessage(sprintf('Id %d not found', $id));
            }
        }

        return $result;
    }
}

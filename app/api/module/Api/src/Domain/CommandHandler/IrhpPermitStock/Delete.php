<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\IrhpPermitStock;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\Exception\NotFoundException;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;

/**
 * Delete an IRHP Permit Stock
 *
 * @author Scott Callaway <scott.callaway@capgemini.com>
 */
final class Delete extends AbstractCommandHandler
{
    protected $repoServiceName = 'IrhpPermitStock';

    /**
     * Delete Command Handler
     *
     * @param CommandInterface $command
     * @return \Dvsa\Olcs\Api\Domain\Command\Result
     * @throws ValidationException
     */
    public function handleCommand(CommandInterface $command)
    {
        $id = $command->getId();
        $stock = $this->getRepo()->fetchById($id);

        if (!$stock->canDelete()) {
            throw new ValidationException(['irhp-permit-stock-cannot-delete-active-dependencies']);
        }

        try {
            $this->getRepo()->delete($stock);
            $this->result->addId('id', $id);
            $this->result->addMessage(sprintf('Permit Stock Deleted', $id));
        } catch (NotFoundException $e) {
            $this->result->addMessage(sprintf('Id %d not found', $id));
        }

        return $this->result;
    }
}

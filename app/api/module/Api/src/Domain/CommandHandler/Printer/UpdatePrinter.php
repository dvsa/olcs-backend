<?php

/**
 * Update Printer
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Printer;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;

/**
 * Update Printer
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
final class UpdatePrinter extends AbstractCommandHandler
{
    protected $repoServiceName = 'Printer';

    public function handleCommand(CommandInterface $command)
    {
        $printer = $this->getRepo()->fetchUsingId($command);

        $printer->setPrinterName($command->getPrinterName());
        $printer->setPrinterTray($command->getPrinterTray());
        $printer->setDescription($command->getDescription());
        $this->getRepo()->save($printer);

        $result = new Result();
        $result->addId('printer', $printer->getId());
        $result->addMessage('Printer updated successfully');

        return $result;
    }
}

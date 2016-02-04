<?php

/**
 * Delete a printer
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Printer;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\Exception\ValidationException;
use Dvsa\Olcs\Api\Entity\PrintScan\Printer as PrinterEntity;

/**
 * Delete a printer
 *
 * @author Alex Peshkov <alex.peshkov@valtech.co.uk>
 */
final class DeletePrinter extends AbstractCommandHandler
{
    protected $repoServiceName = 'Printer';

    public function handleCommand(CommandInterface $command)
    {
        $printer = $this->getRepo()->fetchWithTeams($command->getId());
        $this->validatePrinter($printer);

        $result = new Result();

        // dry run, just need to validate if we can remove the printer
        if ($command->getValidate()) {
            $result->addMessage('Ready to remove');
            return $result;
        }

        $this->getRepo()->delete($printer);
        $result->addId('printer', $printer->getId());
        $result->addMessage('Printer deleted successfully');
        return $result;
    }

    protected function validatePrinter($printer)
    {
        $errors = [];
        if (count($printer->getTeams())) {
            $errors[PrinterEntity::ERROR_TEAMS_EXISTS] =
                'You cannot delete a printer that is allocated to a team or user';
        }

        if ($errors) {
            throw new ValidationException($errors);
        }
    }
}

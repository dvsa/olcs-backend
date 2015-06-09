<?php

/**
 * Delete Workshop
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Application;

use Dvsa\Olcs\Api\Domain\Command\Application\UpdateApplicationCompletion;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Transfer\Command\Workshop\DeleteWorkshop as WorkshopDeleteWorkshop;

/**
 * Delete Workshop
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class DeleteWorkshop extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'Application';

    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();

        $result->merge($this->handleSideEffect(WorkshopDeleteWorkshop::create(['ids' => $command->getIds()])));

        $data = ['id' => $command->getApplication(), 'section' => 'safety'];

        $result->merge($this->handleSideEffect(UpdateApplicationCompletion::create($data)));

        return $result;
    }
}

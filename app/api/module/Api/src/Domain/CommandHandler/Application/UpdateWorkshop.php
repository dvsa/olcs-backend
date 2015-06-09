<?php

/**
 * Update Workshop
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Application;

use Dvsa\Olcs\Api\Domain\Command\Application\UpdateApplicationCompletion;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Transfer\Command\Workshop\UpdateWorkshop as WorkshopUpdateWorkshop;

/**
 * Update Workshop
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class UpdateWorkshop extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'Application';

    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();

        $result->merge($this->handleSideEffect(WorkshopUpdateWorkshop::create($command->getArrayCopy())));

        $completionData = ['id' => $command->getApplication(), 'section' => 'safety'];
        $result->merge($this->handleSideEffect(UpdateApplicationCompletion::create($completionData)));

        return $result;
    }
}

<?php

/**
 * Create Workshop
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Application;

use Dvsa\Olcs\Api\Domain\Command\Application\UpdateApplicationCompletion;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Transfer\Command\Workshop\CreateWorkshop as WorkshopCreateWorkshop;
use Dvsa\Olcs\Api\Entity\Application\Application;

/**
 * Create Workshop
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class CreateWorkshop extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'Application';

    public function handleCommand(CommandInterface $command)
    {
        /** @var Application $application */
        $application = $this->getRepo()->fetchById($command->getApplication());

        $result = new Result();

        $data = $command->getArrayCopy();
        $data['licence'] = $application->getLicence()->getId();

        $result->merge($this->handleSideEffect(WorkshopCreateWorkshop::create($data)));

        $completionData = ['id' => $command->getApplication(), 'section' => 'safety'];
        $result->merge($this->handleSideEffect(UpdateApplicationCompletion::create($completionData)));

        return $result;
    }
}

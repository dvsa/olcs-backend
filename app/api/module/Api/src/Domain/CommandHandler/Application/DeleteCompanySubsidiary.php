<?php

/**
 * Delete Company Subsidiary
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Application;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\Application\Application;
use Dvsa\Olcs\Transfer\Command\Application\DeleteCompanySubsidiary as Cmd;
use Dvsa\Olcs\Transfer\Command\Licence\DeleteCompanySubsidiary as LicenceDeleteCompanySubsidiary;
use Dvsa\Olcs\Api\Domain\Command\Application\UpdateApplicationCompletion as UpdateApplicationCompletionCommand;

/**
 * Delete Company Subsidiary
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class DeleteCompanySubsidiary extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'Application';

    public function handleCommand(CommandInterface $command)
    {
        /** @var Application $application */
        $application = $this->getRepo()->fetchById($command->getApplication());

        $this->result->merge($this->deleteCompanySubsidiary($command, $application->getLicence()));
        $this->result->merge($this->updateApplicationCompletion($command));

        return $this->result;
    }

    private function deleteCompanySubsidiary(Cmd $command, Licence $licence)
    {
        $data = $command->getArrayCopy();
        $data['licence'] = $licence->getId();

        return $this->handleSideEffect(LicenceDeleteCompanySubsidiary::create($data));
    }

    private function updateApplicationCompletion(Cmd $command)
    {
        return $this->handleSideEffect(
            UpdateApplicationCompletionCommand::create(
                [
                    'id' => $command->getApplication(),
                    'section' => 'businessDetails',
                    'data' => [
                        'hasChanged' => true
                    ]
                ]
            )
        );
    }
}

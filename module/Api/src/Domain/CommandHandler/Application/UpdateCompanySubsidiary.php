<?php

/**
 * Update Company Subsidiary
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Application;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\Application\Application;
use Dvsa\Olcs\Transfer\Command\Application\UpdateCompanySubsidiary as Cmd;
use Dvsa\Olcs\Transfer\Command\Licence\UpdateCompanySubsidiary as LicenceUpdateCompanySubsidiary;
use Dvsa\Olcs\Api\Domain\Command\Application\UpdateApplicationCompletion as UpdateApplicationCompletionCommand;

/**
 * Update Company Subsidiary
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class UpdateCompanySubsidiary extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'Application';

    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();

        /** @var Application $application */
        $application = $this->getRepo()->fetchById($command->getApplication());

        $updateResult = $this->updateCompanySubsidiary($command, $application->getLicence());
        $result->merge($updateResult);
        $result->merge($this->updateApplicationCompletion($command, $updateResult->getFlag('hasChanged')));

        return $result;
    }

    private function updateCompanySubsidiary(Cmd $command, Licence $licence)
    {
        $data = $command->getArrayCopy();
        $data['licence'] = $licence->getId();

        return $this->handleSideEffect(LicenceUpdateCompanySubsidiary::create($data));
    }

    private function updateApplicationCompletion(Cmd $command, $hasChanged)
    {
        return $this->handleSideEffect(
            UpdateApplicationCompletionCommand::create(
                [
                    'id' => $command->getApplication(),
                    'section' => 'businessDetails',
                    'data' => [
                        'hasChanged' => $hasChanged
                    ]
                ]
            )
        );
    }
}

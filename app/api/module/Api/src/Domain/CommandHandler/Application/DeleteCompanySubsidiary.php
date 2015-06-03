<?php

/**
 * Delete Company Subsidiary
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Application;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\Application\Application;
use Dvsa\Olcs\Transfer\Command\Application\DeleteCompanySubsidiary as Cmd;
use Dvsa\Olcs\Transfer\Command\Licence\DeleteCompanySubsidiary as LicenceDeleteCompanySubsidiary;
use Dvsa\Olcs\Api\Domain\Command\Application\UpdateApplicationCompletion;

/**
 * Delete Company Subsidiary
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class DeleteCompanySubsidiary extends AbstractCommandHandler
{
    protected $repoServiceName = 'Application';

    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();

        /** @var Application $application */
        $application = $this->getRepo()->fetchById($command->getApplication());

        try {

            $this->getRepo()->beginTransaction();

            $result->merge($this->deleteCompanySubsidiary($command, $application->getLicence()));

            $result->merge($this->updateApplicationCompletion($command));

            $this->getRepo()->commit();

            return $result;

        } catch (\Exception $ex) {
            $this->getRepo()->rollback();
            throw $ex;
        }
    }

    private function deleteCompanySubsidiary(Cmd $command, Licence $licence)
    {
        $data = $command->getArrayCopy();
        $data['licence'] = $licence->getId();

        return $this->getCommandHandler()->handleCommand(LicenceDeleteCompanySubsidiary::create($data));
    }

    private function updateApplicationCompletion(Cmd $command)
    {
        return $this->getCommandHandler()->handleCommand(
            UpdateApplicationCompletion::create(['id' => $command->getApplication(), 'section' => 'businessDetails'])
        );
    }
}

<?php

/**
 * Validate Application
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Application\Grant;

use Dvsa\Olcs\Api\Domain\Command\Application\Grant\CommonGrant as CommonGrantCmd;
use Dvsa\Olcs\Api\Domain\Command\Application\Grant\CopyApplicationDataToLicence as CopyApplicationDataToLicenceCmd;
use Dvsa\Olcs\Api\Domain\Command\Application\Grant\CreateDiscRecords as CreateDiscRecordsCmd;
use Dvsa\Olcs\Api\Domain\Command\Application\Grant\ProcessApplicationOperatingCentres as ProcessAocCmd;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Transfer\Command\Application\CreateSnapshot;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;

/**
 * Validate Application
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class ValidateApplication extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'Application';

    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();

        /** @var ApplicationEntity $application */
        $application = $this->getRepo()->fetchUsingId($command);

        $result->merge($this->createSnapshot($application->getId()));

        $application->setStatus(
            $this->getRepo()->getRefdataReference(ApplicationEntity::APPLICATION_STATUS_VALID)
        );
        $this->getRepo()->save($application);

        $currentTotAuth = $application->getLicence()->getTotAuthVehicles();

        $result->merge($this->proxyCommand($command, CopyApplicationDataToLicenceCmd::class));
        $result->merge($this->proxyCommand($command, ProcessAocCmd::class));
        $result->merge($this->proxyCommand($command, CommonGrantCmd::class));

        $data = $command->getArrayCopy();
        $data['currentTotAuth'] = $currentTotAuth;

        $result->merge($this->handleSideEffect(CreateDiscRecords::create($data)));

        return $result;
    }

    protected function createSnapshot($applicationId)
    {
        $data = [
            'id' => $applicationId,
            'event' => CreateSnapshot::ON_GRANT
        ];

        return $this->handleSideEffect(CreateSnapshot::create($data));
    }
}

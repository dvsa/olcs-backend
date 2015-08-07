<?php

/**
 * Grant Psv
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Application;

use Dvsa\Olcs\Api\Domain\Command\Application\Grant\CommonGrant;
use Dvsa\Olcs\Api\Domain\Command\Application\Grant\CopyApplicationDataToLicence;
use Dvsa\Olcs\Api\Domain\Command\Application\Grant\CreateDiscRecords;
use Dvsa\Olcs\Api\Domain\Command\Application\Grant\ProcessApplicationOperatingCentres;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;
use Dvsa\Olcs\Transfer\Command\Application\CreateSnapshot as CreateSnapshotCmd;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;
use Dvsa\Olcs\Api\Entity\Licence\Licence;

/**
 * Grant Psv
 *
 * @author Rob Caiger <rob@clocal.co.uk>
 */
final class GrantPsv extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'Application';

    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();

        /** @var ApplicationEntity $application */
        $application = $this->getRepo()->fetchUsingId($command);

        $result->merge($this->createSnapshot($application->getId()));

        $this->updateStatusAndDate($application, ApplicationEntity::APPLICATION_STATUS_VALID);
        $result->addMessage('Application status updated');

        $this->updateStatusAndDate($application->getLicence(), Licence::LICENCE_STATUS_VALID);
        $result->addMessage('Licence status updated');

        $this->getRepo()->save($application);

        // Get licence totAuthVehicles before we update
        $currentTotAuth = $application->getLicence()->getTotAuthVehicles();

        $result->merge($this->proxyCommand($command, CopyApplicationDataToLicence::class));

        $data = $command->getArrayCopy();
        $data['currentTotAuth'] = $currentTotAuth;

        $result->merge($this->handleSideEffect(CreateDiscRecords::create($data)));

        if (!$application->isSpecialRestricted()) {
            $result->merge($this->proxyCommand($command, ProcessApplicationOperatingCentres::class));
        }

        $result->merge($this->proxyCommand($command, CommonGrant::class));

        return $result;
    }

    protected function createSnapshot($applicationId)
    {
        $data = [
            'id' => $applicationId,
            'event' => CreateSnapshotCmd::ON_GRANT
        ];

        return $this->handleSideEffect(CreateSnapshotCmd::create($data));
    }

    /**
     * @param ApplicationEntity|Licence $entity
     * @param $status
     */
    protected function updateStatusAndDate($entity, $status)
    {
        $entity->setStatus($this->getRepo()->getRefdataReference($status));
        $entity->setGrantedDate(new DateTime());
    }
}

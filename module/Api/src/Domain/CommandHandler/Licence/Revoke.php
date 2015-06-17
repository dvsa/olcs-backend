<?php

/**
 * Revoke a licence
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Licence;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Entity\Licence\Licence;

/**
 * Revoke a licence
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
final class Revoke extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'Licence';

    public function handleCommand(CommandInterface $command)
    {
        /* @var $licence Licence */
        $licence = $this->getRepo()->fetchUsingId($command);
        $licence->setStatus($this->getRepo()->getRefdataReference(Licence::LICENCE_STATUS_REVOKED));
        $licence->setRevokedDate(new \DateTime());

        // @todo In old system Revoking a licence also did:
        //$terminateData = $licenceService->getRevocationDataForLicence($row['licence']['id']);
        //$licenceStatusHelperService->ceaseDiscs($terminateData);
        //$licenceStatusHelperService->removeLicenceVehicles($terminateData['licenceVehicles']);
        //$licenceStatusHelperService->removeTransportManagers($terminateData['tmLicences']);

        $this->getRepo()->save($licence);

        $result = new Result();
        $result->addMessage("Licence ID {$licence->getId()} revoked");

        return $result;
    }
}

<?php

/**
 * Process Licence Status Rules - to Valid
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\LicenceStatusRule;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;

/**
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
final class ProcessToValid extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'LicenceStatusRule';

    public function handleCommand(CommandInterface $command)
    {
        // command not required, unset to remove PMD error
        unset($command);

        $result = new Result();
        $licencesToAction = $this->getRepo('LicenceStatusRule')->fetchToValid(
            (new \DateTime())->format(\DateTime::W3C)
        );

        /* @var $licenceStatusRule \Dvsa\Olcs\Api\Entity\Licence\LicenceStatusRule */
        foreach ($licencesToAction as $licenceStatusRule) {

            // if licence is not curtailed or suspended, then continue
            if ($licenceStatusRule->getLicence()->getStatus()->getId() !== Licence::LICENCE_STATUS_CURTAILED &&
                $licenceStatusRule->getLicence()->getStatus()->getId() !== Licence::LICENCE_STATUS_SUSPENDED
            ) {
                $result->addMessage(
                    "To Valid Licence Status Rule ID {$licenceStatusRule->getId()} licence "
                    ."is not curtailed or suspended"
                );
                continue;
            }

            // update section26 on licenced vehicles
            /* @var $licenceVehicle \Dvsa\Olcs\Api\Entity\Licence\LicenceVehicle */
            foreach ($licenceStatusRule->getLicence()->getLicenceVehicles() as $licenceVehicle) {
                $licenceVehicle->getVehicle()->setSection26(0);
            }

            $licenceStatusRule->getLicence()->setStatus(
                $this->getRepo()->getRefdataReference(Licence::LICENCE_STATUS_VALID)
            );
            $licenceStatusRule->getLicence()->setRevokedDate(null);
            $licenceStatusRule->getLicence()->setCurtailedDate(null);
            $licenceStatusRule->getLicence()->setSuspendedDate(null);

            $licenceStatusRule->setEndProcessedDate(new \DateTime());
            $this->getRepo()->save($licenceStatusRule);

            $result->addMessage("To Valid Licence Status Rule ID {$licenceStatusRule->getId()} success");
        }

        return $result;
    }
}

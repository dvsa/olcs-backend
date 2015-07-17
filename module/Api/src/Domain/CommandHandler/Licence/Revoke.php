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

use Dvsa\Olcs\Api\Domain\Command\Discs\CeasePsvDiscs;
use Dvsa\Olcs\Api\Domain\Command\Discs\CeaseGoodsDiscs;
use Dvsa\Olcs\Api\Domain\Command\LicenceVehicle\RemoveLicenceVehicle;
use Dvsa\Olcs\Api\Domain\Command\Tm\DeleteTransportManagerLicence;
use Dvsa\Olcs\Api\Domain\Command\LicenceStatusRule\RemoveLicenceStatusRulesForLicence;

/**
 * Revoke a licence
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 */
final class Revoke extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'Licence';

    public function handleCommand(CommandInterface $command)
    {
        /** @var Licence $licence */
        $licence = $this->getRepo()->fetchUsingId($command);
        $licence->setStatus($this->getRepo()->getRefdataReference(Licence::LICENCE_STATUS_REVOKED));
        $licence->setRevokedDate(new \DateTime());

        if ($licence->getGoodsOrPsv()->getId() === Licence::LICENCE_CATEGORY_GOODS_VEHICLE) {
            $commandCeaseDiscs = CeaseGoodsDiscs::create(
                [
                    'licenceVehicles' => $licence->getLicenceVehicles()
                ]
            );
        } else {
            $commandCeaseDiscs = CeasePsvDiscs::create(
                [
                    'discs' => $licence->getPsvDiscs()
                ]
            );
        }

        $result = new Result();
        $result->merge($this->handleSideEffect($commandCeaseDiscs));

        $result->merge(
            $this->handleSideEffect(
                RemoveLicenceVehicle::create(
                    [
                        'licenceVehicles' => $licence->getLicenceVehicles()
                    ]
                )
            )
        );

        $result->merge(
            $this->handleSideEffect(
                DeleteTransportManagerLicence::create(
                    [
                        'licence' => $licence
                    ]
                )
            )
        );

        if ($command->getDeleteLicenceStatusRules()) {
            $result->merge(
                $this->handleSideEffect(
                    RemoveLicenceStatusRulesForLicence::create(
                        [
                        'licence' => $licence
                        ]
                    )
                )
            );
        }

        $this->getRepo()->save($licence);
        $result->addMessage("Licence ID {$licence->getId()} revoked");

        return $result;
    }
}

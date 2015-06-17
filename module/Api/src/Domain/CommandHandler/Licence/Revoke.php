<?php

/**
 * Revoke a licence
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Licence;

use Dvsa\Olcs\Api\Domain\Command\Discs\RemoveLicenceVehicleVehicles;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Entity\Licence\Licence;

use Dvsa\Olcs\Api\Domain\Command\Discs\CeasePsvDiscs;
use Dvsa\Olcs\Api\Domain\Command\Discs\CeaseGoodsDiscs;
use Dvsa\Olcs\Api\Domain\Command\LicenceVehicle\RemoveLicenceVehicle;
use Dvsa\Olcs\Api\Domain\Command\Tm\DeleteTransportManagerLicence;

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

        $discsCommand = (
            $licence->getGoodsOrPsv()->getId() === Licence::LICENCE_CATEGORY_GOODS_VEHICLE ?
            CeaseGoodsDiscs::class : CeasePsvDiscs::class
        );

        $command = $discsCommand::create(
            [
                'licence' => $licence
            ]
        );

      $this->getCommandHandler()->handleCommand($command);

        $this->getCommandHandler()->handleCommand(
            RemoveLicenceVehicle::create(
                [
                    'licence' => $licence
                ]
            )
        );

        $this->getCommandHandler()->handleCommand(
            DeleteTransportManagerLicence::create(
                [
                    'licence' => $licence
                ]
            )
        );

        $this->getRepo()->save($licence);

        $result = new Result();
        $result->addMessage("Licence ID {$licence->getId()} revoked");

        return $result;
    }
}

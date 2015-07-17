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

/**
 * Surrender a licence
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 * @author Josh Curtis <josh.curtis@valtech.co.uk>
 */
final class Surrender extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'Licence';

    public function handleCommand(CommandInterface $command)
    {
        /* @var $licence Licence */
        $licence = $this->getRepo()->fetchUsingId($command);

        if ($command->isTerminated() == true) {
            $status = Licence::LICENCE_STATUS_TERMINATED;
        }else{
            $status = Licence::LICENCE_STATUS_SURRENDERED;
        }

        $licence->setStatus($this->getRepo()->getRefdataReference($status));
        $licence->setSurrenderedDate(new \DateTime($command->getSurrenderDate()));

        $discsCommand = (
            $licence->getGoodsOrPsv()->getId() === Licence::LICENCE_CATEGORY_GOODS_VEHICLE ?
            CeaseGoodsDiscs::class : CeasePsvDiscs::class
        );

        $result = new Result();
        $command = $discsCommand::create(
            [
                'licence' => $licence
            ]
        );
        $result->merge($this->handleSideEffect($command));

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

        $this->getRepo()->save($licence);
        $result->addMessage("Licence ID {$licence->getId()} surrendered");

        return $result;
    }
}

<?php

/**
 * Process Continuation Not Sought
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Licence;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Command\Discs\CeaseGoodsDiscs;
use Dvsa\Olcs\Api\Domain\Command\Discs\CeasePsvDiscs;
use Dvsa\Olcs\Api\Domain\Command\Licence\ExpireAllCommunityLicences as ExpireComLics;
use Dvsa\Olcs\Api\Domain\Command\LicenceVehicle\RemoveLicenceVehicle;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\Command\Tm\DeleteTransportManagerLicence;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Entity\Licence\Licence as Entity;
use Dvsa\Olcs\Transfer\Command\CommandInterface;

/**
 * Process Continuation Not Sought
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
final class ProcessContinuationNotSought extends AbstractCommandHandler
{
    protected $repoServiceName = 'Licence';

    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();

        /** @var Licence $licence */
        $licence = $this->getRepo()->fetchUsingId($command, Query::HYDRATE_OBJECT, $command->getVersion());

        $discsCommandClass = ($licence->isGoods() ? CeaseGoodsDiscs::class : CeasePsvDiscs::class);
        $discsCommand = $discsCommandClass::create(['licence' => $licence]);

        $result->merge(
            $this->handleSideEffects(
                [
                    // Remove any vehicles
                    RemoveLicenceVehicle::create(['licenceVehicles' => $licence->getLicenceVehicles()]),
                    // Unlink any Transport Managers
                    DeleteTransportManagerLicence::create(['licence' => $licence ]),
                    // Expire community licences that are of status 'Pending', 'Active' or 'Suspended'
                    ExpireComLics::create(['id' => $licence->getId()]),
                    // Void any discs associated to vehicles linked to the licence
                    $discsCommand
                ]
            )
        );

        // Set status to CNS
        $licence->setStatus($this->getRepo()->getRefdataReference(Entity::LICENCE_STATUS_CONTINUATION_NOT_SOUGHT));
        $this->getRepo()->save($licence);
        $result->addMessage('Licence updated');

        // @TODO email sending

        return $result;
    }
}

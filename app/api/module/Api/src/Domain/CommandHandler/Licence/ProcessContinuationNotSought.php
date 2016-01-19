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
use Dvsa\Olcs\Api\Domain\Command\Publication\Licence as PublicationLicenceCmd;

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

        $discsCommand = $this->createDiscsCommand($licence);

        // Set status to CNS
        $licence->setStatus($this->getRepo()->getRefdataReference(Entity::LICENCE_STATUS_CONTINUATION_NOT_SOUGHT));
        $this->getRepo()->save($licence);

        $result->merge(
            $this->handleSideEffects(
                [
                    // Remove any vehicles
                    RemoveLicenceVehicle::create(['licence' => $licence->getId()]),
                    // Unlink any Transport Managers
                    DeleteTransportManagerLicence::create(['licence' => $licence->getId()]),
                    // Expire community licences that are of status 'Pending', 'Active' or 'Suspended'
                    ExpireComLics::create(['id' => $licence->getId()]),
                    // Void any discs associated to vehicles linked to the licence
                    $discsCommand,
                    // Create publication for a licence
                    PublicationLicenceCmd::create(['id' => $licence->getId()])
                ]
            )
        );

        $result->addMessage('Licence updated');

        return $result;
    }

    private function createDiscsCommand($licence)
    {
        if ($licence->isGoods()) {
            return CeaseGoodsDiscs::create(['licence' => $licence->getId()]);
        }

        return CeasePsvDiscs::create(['discs' => $licence->getPsvDiscs()]);
    }
}

<?php

/**
 * Revoke a licence
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Licence;

use Dvsa\Olcs\Api\Domain\Command\Application\DeleteApplication;
use Dvsa\Olcs\Api\Domain\Command\Licence\ReturnAllCommunityLicences as ReturnComLics;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Entity\Application\Application;
use Dvsa\Olcs\Transfer\Command\Application\RefuseApplication;
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

    /**
     * @todo I don't think ceasing discs works... neither CeaseGoodsDiscs nor CeasePsvDiscs
     * takes a licence or licence id parameter!!??
     * @see Dvsa\Olcs\Api\Domain\CommandHandler\Licence\ProcessContinuationNotSought::createDiscsCommand()
     */
    public function handleCommand(CommandInterface $command)
    {
        /* @var $licence Licence */
        $licence = $this->getRepo()->fetchUsingId($command);

        if ($command->isTerminated() == true) {
            $status = Licence::LICENCE_STATUS_TERMINATED;
        } else {
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

        $communityLicences = $licence->getCommunityLics()->toArray();
        if (!empty($communityLicences)) {
            $result->merge(
                $this->handleSideEffect(
                    ReturnComLics::create(
                        [
                            'id' => $licence->getId(),
                        ]
                    )
                )
            );
        }

        foreach ($licence->getApplications() as $application) {
            if ($application->getIsVariation()) {
                switch ($application->getStatus()->getId()) {
                    case Application::APPLICATION_STATUS_NOT_SUBMITTED:
                        $result->merge(
                            $this->handleSideEffect(
                                DeleteApplication::create(['id' => $application->getId()])
                            )
                        );
                        break;
                    case Application::APPLICATION_STATUS_UNDER_CONSIDERATION:
                        $result->merge(
                            $this->handleSideEffect(
                                RefuseApplication::create(['id' => $application->getId()])
                            )
                        );
                        break;
                    default:
                        break;
                }
            }
        }

        $this->getRepo()->save($licence);
        $result->addMessage("Licence ID {$licence->getId()} surrendered");

        return $result;
    }
}

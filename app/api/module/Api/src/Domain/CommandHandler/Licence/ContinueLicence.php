<?php

/**
 * ContinueLicence
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Licence;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Entity\Licence\Licence;
use Dvsa\Olcs\Api\Entity\Licence\ContinuationDetail;
use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Util\DateTime\DateTime;

/**
 * ContinueLicence
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
final class ContinueLicence extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'Licence';
    protected $extraRepos = ['ContinuationDetail'];

    public function handleCommand(CommandInterface $command)
    {
        /* @var $licence Licence */
        // check Licence ID and version
        $licence = $this->getRepo()->fetchById($command->getId(), Query::HYDRATE_OBJECT, $command->getVersion());

        $continuationDetails = $this->getRepo('ContinuationDetail')->fetchForLicence($command->getId());
        if (count($continuationDetails) === 0) {
            throw new \Dvsa\Olcs\Api\Domain\Exception\RuntimeException('No ContinuationDetail found');
        }
        $continuationDetail = $continuationDetails[0];

        //Add 5 years to the continuation and review dates
        $licence->setExpiryDate((new DateTime($licence->getExpiryDate()))->modify('+5 years'));
        $licence->setReviewDate((new DateTime($licence->getReviewDate()))->modify('+5 years'));

        $result = new Result();
        if ($licence->isGoods()) {
            $this->processGoods($licence, $continuationDetail, $result);
        } else {
            $this->processPsv($licence, $continuationDetail, $result);
        }
        $this->getRepo()->save($licence);

        // Print licence
        $result->merge(
            $this->handleSideEffect(
                \Dvsa\Olcs\Transfer\Command\Licence\PrintLicence::create(['id' => $licence->getId()])
            )
        );

        $continuationDetail->setStatus(
            $this->getRepo()->getRefdataReference(ContinuationDetail::STATUS_COMPLETE)
        );
        $this->getRepo('ContinuationDetail')->save($continuationDetail);

        $result->addMessage('Licence ' . $licence->getId() . ' continued');

        return $result;
    }

    /**
     * Continue licence part for a PSV
     *
     * @param Licence            $licence            Licence
     * @param ContinuationDetail $continuationDetail ContinuationDetail
     * @param Result             $result             Result to add message to
     *
     * @return void
     */
    private function processPsv(Licence $licence, ContinuationDetail $continuationDetail, Result $result)
    {
        // don't do Special Restricted
        if ($licence->isSpecialRestricted()) {
            return;
        }

        // Update the vehicle authorisation to the value entered
        $licence->setTotAuthVehicles($continuationDetail->getTotAuthVehicles());

        // Void any discs
        $result->merge(
            $this->handleSideEffect(
                \Dvsa\Olcs\Api\Domain\Command\Discs\CeasePsvDiscs::create(['discs' => $licence->getPsvDiscs()])
            )
        );

        // Create 'X' new PSV discs where X is the number of discs requested
        $result->merge(
            $this->handleSideEffect(
                \Dvsa\Olcs\Transfer\Command\Licence\CreatePsvDiscs::create(
                    ['licence' => $licence->getId(), 'amount' => $continuationDetail->getTotPsvDiscs()]
                )
            )
        );

        // If licence type is Restricted or Standard International
        if ($licence->isRestricted() || $licence->isStandardInternational()) {

            //Void all community licences
            $result->merge(
                $this->handleSideEffect(
                    \Dvsa\Olcs\Api\Domain\Command\Licence\VoidAllCommunityLicences::create(['id' => $licence->getId()])
                )
            );

            // Generate 'X' new Community licences where X is the number of community licences requested
            // plus the office copy
            $result->merge(
                $this->handleSideEffect(
                    \Dvsa\Olcs\Transfer\Command\CommunityLic\Licence\Create::create(
                        [
                            'licence' => $licence->getId(),
                            'totalLicences' => $continuationDetail->getTotCommunityLicences()
                        ]
                    )
                )
            );
        }
    }

    /**
     * Continue licence part for a Goods
     *
     * @param Licence            $licence            Licence
     * @param ContinuationDetail $continuationDetail ContinuationDetail
     * @param Result             $result             Result to add message to
     *
     * @return void
     */
    private function processGoods(Licence $licence, ContinuationDetail $continuationDetail, Result $result)
    {
        //Void any discs
        $result->merge(
            $this->handleSideEffect(
                \Dvsa\Olcs\Api\Domain\Command\Discs\CeaseGoodsDiscs::create(
                    ['licenceVehicles' => $licence->getLicenceVehicles()]
                )
            )
        );

        //Create a new Goods disc for each vehicle that has a specified date (and is not ceased)
        $licencedVehicleIds = [];
        foreach ($licence->getLicenceVehicles() as $licencedVehicle) {
            $licencedVehicleIds[] = $licencedVehicle->getId();
        }
        $result->merge(
            $this->handleSideEffect(
                \Dvsa\Olcs\Api\Domain\Command\Vehicle\CreateGoodsDiscs::create(
                    ['ids' => $licencedVehicleIds]
                )
            )
        );

        //If licence type is Standard International
        if ($licence->isStandardInternational()) {

            //Void all community licences
            $result->merge(
                $this->handleSideEffect(
                    \Dvsa\Olcs\Api\Domain\Command\Licence\VoidAllCommunityLicences::create(['id' => $licence->getId()])
                )
            );

            //Generate 'X' new Community licences where X is the number of community licences requested
            // plus the office copy
            $result->merge(
                $this->handleSideEffect(
                    \Dvsa\Olcs\Transfer\Command\CommunityLic\Licence\Create::create(
                        [
                            'licence' => $licence->getId(),
                            'totalLicences' => $continuationDetail->getTotCommunityLicences()
                        ]
                    )
                )
            );
        }
    }
}

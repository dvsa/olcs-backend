<?php

/**
 * Create Impounding
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Cases\Impounding;

use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Entity\Cases\Impounding;
use Dvsa\Olcs\Api\Entity\Cases\Cases;
use Dvsa\Olcs\Api\Entity\Venue;
use Dvsa\Olcs\Api\Entity\Cases\Cases as CasesEntity;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Entity\Application\Application as ApplicationEntity;
use Dvsa\Olcs\Api\Entity\Pi\PiVenue;
use Dvsa\Olcs\Transfer\Command\Cases\Impounding\CreateImpounding as Cmd;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\Command\Publication\Impounding as PublishImpoundingCmd;

/**
 * Create Impounding
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
final class CreateImpounding extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'Impounding';

    /**
     * @var CasesEntity $case
     */
    private $case;

    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();

        $impounding = $this->createImpoundingObject($command);

        $this->getRepo()->save($impounding);
        $id = $impounding->getId();
        $result->addMessage('Impounding created');
        $result->addId('impounding', $id);

        // handle publish
        if ($command->getPublish() === 'Y') {
            $result->merge($this->getCommandHandler()->handleCommand($this->createPublishCommand($id, $command)));
        }

        return $result;
    }

    /**
     * @param Cmd $command
     * @return Impounding
     */
    private function createImpoundingObject(Cmd $command)
    {
        $impounding = new Impounding(
            $this->getRepo()->getReference(CasesEntity::class, $command->getCase()),
            $this->getRepo()->getRefdataReference($command->getImpoundingType())
        );

        $venue = $command->getVenue();
        if (!empty($venue) && $venue !== Impounding::VENUE_OTHER) {
            $venue = $this->getRepo()->getReference(Venue::class, $command->getVenue());
        }
        $impounding->setVenueProperties(
            $venue,
            $command->getVenueOther()
        );

        $impoundingLegislationTypes = $this->generateImpoundingLegislationTypes(
            $command->getImpoundingLegislationTypes()
        );

        $impounding->setImpoundingLegislationTypes($impoundingLegislationTypes);

        if ($command->getApplicationReceiptDate() !== null) {
            $impounding->setApplicationReceiptDate(new \DateTime($command->getApplicationReceiptDate()));
        }

        if ($command->getVrm() !== null) {
            $impounding->setVrm($command->getVrm());
        }

        if ($command->getHearingDate() !== null) {
            $impounding->setHearingDate(new \DateTime($command->getHearingDate()));
        }

        if ($command->getPresidingTc() !== null) {
            $impounding->setPresidingTc($this->getRepo()->getRefdataReference($command->getPresidingTc()));
        }

        if ($command->getOutcome() !== null) {
            $impounding->setOutcome($this->getRepo()->getRefdataReference($command->getOutcome()));
        }

        if ($command->getOutcomeSentDate() !== null) {
            $impounding->setOutcomeSentDate(new \DateTime($command->getOutcomeSentDate()));
        }

        if ($command->getNotes() !== null) {
            $impounding->setNotes($command->getNotes());
        }

        return $impounding;
    }

    /**
     * Returns collection of legislation types.
     *
     * @param null $impoundingLegislationTypes
     * @return ArrayCollection
     */
    private function generateImpoundingLegislationTypes($impoundingLegislationTypes = null)
    {
        $result = new ArrayCollection();
        if (!empty($impoundingLegislationTypes)) {
            foreach ($impoundingLegislationTypes as $legislationType) {
                $result->add($this->getRepo()->getRefdataReference($legislationType));
            }
        }
        return $result;
    }

    /**
     * @param int $id
     * @param Cmd $command
     * @return PublishImpoundingCmd
     */
    private function createPublishCommand($id, $command)
    {
        /** @var CasesEntity $case */
        $this->case = $this->getRepo()->getReference(CasesEntity::class, $command->getCase());

        $caseType = $this->case->getCaseType()->getId();

        $commandData =                 [
            'id' => $id,
            'trafficArea' => $this->determinePubTrafficArea($command)->getId(),
            'pi' => $this->determinePublicInquiry()
        ];

        if ($caseType === CasesEntity::APP_CASE_TYPE) {
            /** @var ApplicationEntity $application */
            $application = $this->case->getApplication();

            $licType = $application->getGoodsOrPsv()->getId();

            return PublishImpoundingCmd::create(
                array_merge(
                    $commandData,
                    [
                        'pubType' => [$this->determinePubType($licType)],
                        'application' => $application->getId()
                    ]
                )
            );
        } else {
            /** @var LicenceEntity $licence */
            $licence = $this->case->getLicence();

            $licType = $licence->getGoodsOrPsv()->getId();

            return PublishImpoundingCmd::create(
                array_merge(
                    $commandData,
                    [
                        'pubType' => [$this->determinePubType($licType)],
                        'licence' => $licence->getId()
                    ]
                )
            );
        }
    }

    /**
     * Determine the publication type, IF Goods => 'A&D' else 'N&P'
     *
     * @param $licType
     * @return string
     */
    private function determinePubType($licType)
    {
        return ($licType == LicenceEntity::LICENCE_CATEGORY_GOODS_VEHICLE) ? 'A&D' : 'N&P';
    }

    /**
     * Determine the publication traffic area, derived from the licence
     *
     * @param Cmd $command
     * @return \Dvsa\Olcs\Api\Entity\TrafficArea\TrafficArea
     */
    private function determinePubTrafficArea(Cmd $command)
    {
        if ($this->case->getCaseType()->getId() === CasesEntity::APP_CASE_TYPE) {
            return $this->case->getApplication()->getLicence()->getTrafficArea();
        } else {
            /** @var LicenceEntity $licence */
            return $this->case->getLicence()->getTrafficArea();
        }
    }

    /**
     * Return Public Inquiry Id
     * @return mixed
     */
    private function determinePublicInquiry()
    {
        if (count($this->case->getPublicInquirys()) > 0) {
            return $this->case->getPublicInquirys()[0];
        }

        return null;
    }
}

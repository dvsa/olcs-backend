<?php

/**
 * Update Impounding
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Cases\Impounding;

use Doctrine\ORM\Query;
use Doctrine\Common\Collections\ArrayCollection;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Entity\Cases\Impounding;
use Dvsa\Olcs\Api\Entity\Venue;
use Dvsa\Olcs\Transfer\Command\Cases\Impounding\UpdateImpounding as Cmd;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;

/**
 * Update Impounding
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
final class UpdateImpounding extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'Impounding';

    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();

        $impounding = $this->createImpoundingObject($command);

        $this->getRepo()->save($impounding);

        $result->addMessage('Impounding updated');

        return $result;
    }

    /**
     * @param Cmd $command
     * @return Impounding
     */
    private function createImpoundingObject(Cmd $command)
    {
        $impounding = $this->getRepo()->fetchUsingId($command, Query::HYDRATE_OBJECT, $command->getVersion());

        $impounding->setImpoundingType($this->getRepo()->getRefdataReference($command->getImpoundingType()));

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
}

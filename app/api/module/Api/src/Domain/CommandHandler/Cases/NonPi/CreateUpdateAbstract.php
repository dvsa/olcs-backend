<?php

/**
 * Create Update Abstract
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Cases\NonPi;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\Repository\NonPi as Repository;
use Dvsa\Olcs\Api\Entity as Entities;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Transfer\Command\Cases\NonPi\Update as UpdateCommand;
use Dvsa\Olcs\Transfer\Command\Cases\NonPi\Create as CreateCommand;

/**
 * Create Update Abstract
 */
abstract class CreateUpdateAbstract extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'NonPi';

    /**
     * Sets data from a create or update hearing command
     *
     * @param Entities\Cases\Hearing                                $entity  hearing entity
     * @param CommandInterface|CreateCommand|UpdateCommand $command update or create command
     *
     * @return void
     */
    protected function setData($entity, CommandInterface $command)
    {
        /** @var Repository $repo For traceability */
        $repo = $this->getRepo();

        // Hearing Type - required - no if needed.
        $entity->setHearingType($repo->getRefdataReference($command->getHearingType()));

        // Business logic?
        if ($command->getVenue() !== null) {
            $venue = $this->getRepo()->getReference(Entities\Venue::class, $command->getVenue());
            $entity->setVenue($venue);
            $entity->setVenueOther(null);
        } else {
            $entity->setVenueOther($command->getVenueOther());
            $entity->setVenue(null);
        }

        // Amazingly these foreign keys are optional.

        if ($command->getCase() !== null) {
            $case = $this->getRepo()->getReference(Entities\Cases\Cases::class, $command->getCase());
            $entity->setCase($case);
        }

        // Fields - again, optional...?

        if ($command->getAgreedByTcDate() !== null) {
            $entity->setAgreedByTcDate(new \DateTime($command->getAgreedByTcDate()));
        }

        if ($command->getHearingDate() !== null) {
            $entity->setHearingDate(new \DateTime($command->getHearingDate()));
        }

        //deals with witnesses field being null or empty string
        $witnesses = is_numeric($command->getWitnessCount()) ? $command->getWitnessCount() : 0;
        $entity->setWitnessCount($witnesses);

        if ($command->getOutcome() !== null) {
            $entity->setOutcome($repo->getRefdataReference($command->getOutcome()));
        }

        if ($command->getPresidingStaffName() !== null) {
            $entity->setPresidingStaffName($command->getPresidingStaffName());
        }
    }
}

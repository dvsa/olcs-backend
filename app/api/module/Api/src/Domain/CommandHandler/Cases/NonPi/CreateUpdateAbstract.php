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

/**
 * Create Update Abstract
 */
abstract class CreateUpdateAbstract extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'NonPi';

    protected function setData($entity, CommandInterface $command)
    {
        /* @var $entity Entity For traceability */
        /* @var $command CreateCommand For traceability */

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

        // db expects integer and errors when sending empty string
        if (!empty($command->getWitnessCount())) {
            $entity->setWitnessCount($command->getWitnessCount());
        }

        if ($command->getOutcome() !== null) {
            $entity->setOutcome($repo->getRefdataReference($command->getOutcome()));
        }

        if ($command->getPresidingStaffName() !== null) {
            $entity->setPresidingStaffName($command->getPresidingStaffName());
        }
    }
}

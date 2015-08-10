<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Disqualification;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Entity\Organisation\Disqualification;
use Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Transfer\Command\Disqualification\Create as Command;

/**
 * Creates a Disqualification
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
final class Create extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'Disqualification';

    /**
     * Creates a Disqualification
     *
     * @param CommandInterface $command
     *
     * @return Result
     */
    public function handleCommand(CommandInterface $command)
    {
        /* @var $command Command */

        $errors = $this->validate($command);
        if (!empty($errors)) {
            throw new \Dvsa\Olcs\Api\Domain\Exception\ValidationException($errors);
        }

        $disqualification = new Disqualification();
        $disqualification->setIsDisqualified($command->getIsDisqualified());
        $disqualification->setNotes($command->getNotes());
        if ($command->getOfficerCd()) {
            $disqualification->setOfficerCd(
                $this->getRepo()->getReference(ContactDetails::class, $command->getOfficerCd())
            );
        }
        if ($command->getOrganisation()) {
            $disqualification->setOrganisation(
                $this->getRepo()->getReference(Organisation::class, $command->getOrganisation())
            );
        }
        $disqualification->setStartDate(new \DateTime($command->getStartDate()));
        $disqualification->setPeriod($command->getPeriod() ?: null);

        $this->getRepo()->save($disqualification);

        $result = new Result();

        $result->addId('disqualification', $disqualification->getId());
        $result->addMessage('Disqualification created');

        return $result;
    }

    /**
     * Validate the command params
     *
     * @param Command $command
     *
     * @return array of error messages
     */
    private function validate(Command $command)
    {
        $errors = [];
        if ($command->getIsDisqualified() === 'Y' && empty($command->getStartDate())) {
            $errors['DISQ_START_DATE_MISSING'] = 'Start date must be specified if isDisqualified';
        }

        if (empty($command->getOrganisation()) && empty($command->getOfficerCd())) {
            $errors['DISQ_MISSING_ORG_OFFICER'] = 'Organisation or OfficerCd must be specified';
        }

        if (!empty($command->getOrganisation()) && !empty($command->getOfficerCd())) {
            $errors['DISQ_BOTH_ORG_OFFICER'] = 'You cannot specify both Organisation and OfficerCd';
        }

        return $errors;
    }
}

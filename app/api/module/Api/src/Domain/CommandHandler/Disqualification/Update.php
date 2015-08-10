<?php

namespace Dvsa\Olcs\Api\Domain\CommandHandler\Disqualification;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Transfer\Command\Disqualification\Update as Command;
use Doctrine\ORM\Query;

/**
 * Update a Disqualification
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
final class Update extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'Disqualification';

    /**
     * Update a Disqualification
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

        $disqualification = $this->getRepo()->fetchUsingId($command, Query::HYDRATE_OBJECT, $command->getVersion());
        $disqualification->setIsDisqualified($command->getIsDisqualified());
        $disqualification->setNotes($command->getNotes());
        $disqualification->setStartDate(new \DateTime($command->getStartDate()));
        $disqualification->setPeriod($command->getPeriod() ?: null);

        $this->getRepo()->save($disqualification);

        $result = new Result();
        $result->addId('disqualification', $disqualification->getId());
        $result->addMessage('Disqualification updated');

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

        return $errors;
    }
}

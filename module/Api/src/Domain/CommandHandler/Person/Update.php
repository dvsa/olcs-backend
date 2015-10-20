<?php

/**
 * Update a Person
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Person;

use DateTime;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Entity\Person\Person;
use Dvsa\Olcs\Transfer\Command\CommandInterface;

/**
 * Update a Person
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
final class Update extends AbstractCommandHandler
{
    protected $repoServiceName = 'Person';

    public function handleCommand(CommandInterface $command)
    {
        /* @var $person Person */
        $person = $this->getRepo()->fetchUsingId($command);

        if ($command->getDob()) {
            $person->setBirthDate(new \DateTime($command->getDob()));
        }

        $this->getRepo()->save($person);

        $result = new Result();
        $result->addId("person", $person->getId());
        $result->addMessage("Person ID {$person->getId()} Updated.");

        return $result;
    }
}

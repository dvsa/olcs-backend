<?php

/**
 * UpdatePeople
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Licence;

use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Transfer\Command\CommandInterface;

/**
 * UpdatePeople
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
final class UpdatePeople extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'Person';

    public function handleCommand(CommandInterface $command)
    {
        /* @var $command \Dvsa\Olcs\Transfer\Command\Licence\UpdatePeople */

        $person = $this->getRepo()->fetchById(
            $command->getPerson(),
            \Doctrine\ORM\Query::HYDRATE_OBJECT,
            $command->getVersion()
        );

        $person->updatePerson(
            $command->getForename(),
            $command->getFamilyName(),
            $this->getRepo()->getRefdataReference($command->getTitle()),
            $command->getBirthDate()
        );

        $person->setOtherName($command->getOtherName());
        $this->getRepo()->save($person);

        $result = new Result();
        $result->addId('person', $person->getId());
        $result->addMessage("Person ID {$person->getId()} updated");
        return $result;
    }
}

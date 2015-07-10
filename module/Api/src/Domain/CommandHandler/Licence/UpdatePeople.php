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
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Domain\Exception\BadRequestException;

/**
 * UpdatePeople
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
final class UpdatePeople extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'Licence';
    protected $extraRepos = ['Person'];

    public function handleCommand(CommandInterface $command)
    {
        /* @var $command \Dvsa\Olcs\Transfer\Command\Licence\UpdatePeople */

        /* @var $licence LicenceEntity */
        $licence = $this->getRepo()->fetchUsingId($command);

        if (!$licence->getOrganisation()->isSoleTrader() && !$licence->getOrganisation()->isPartnership() ) {
            throw new BadRequestException('Only sole traders and partnerships can be editied');
        }

        $person = $this->getRepo('Person')->fetchById(
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
        $this->getRepo('Person')->save($person);

        $result = new Result();
        $result->addId('person', $person->getId());
        $result->addMessage("Person ID {$person->getId()} updated");
        return $result;
    }
}

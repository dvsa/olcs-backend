<?php

/**
 * Update PrivateHireLicence
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\PrivateHireLicence;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Doctrine\ORM\Query;
use Dvsa\Olcs\Transfer\Command\PrivateHireLicence\Update as Command;
use Dvsa\Olcs\Api\Entity\Licence\PrivateHireLicence;
use Dvsa\Olcs\Api\Entity\ContactDetails;

/**
 * Update PrivateHireLicence
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
final class Update extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'PrivateHireLicence';

    protected $extraRepos = ['ContactDetails'];

    public function handleCommand(CommandInterface $command)
    {
        /* @var $command Command */

        /* @var $phl PrivateHireLicence */
        $phl = $this->getRepo()->fetchUsingId($command, Query::HYDRATE_OBJECT, $command->getVersion());
        $phl->setPrivateHireLicenceNo($command->getPrivateHireLicenceNo());

        $cd = $phl->getContactDetails();
        $cd->setDescription($command->getCouncilName());

        $address = $cd->getAddress();
        $address->updateAddress(
            $command->getAddress()['addressLine1'],
            $command->getAddress()['addressLine2'],
            $command->getAddress()['addressLine3'],
            $command->getAddress()['addressLine4'],
            $command->getAddress()['town'],
            $command->getAddress()['postcode'],
            $this->getRepo()->getReference(ContactDetails\Country::class, $command->getAddress()['countryCode'])
        );

        $this->getRepo()->save($phl);
        $this->getRepo('ContactDetails')->save($cd);

        $result = new Result();
        $result->addId('privateHireLicence', $phl->getId());
        $result->addMessage("PrivateHireLicence ID {$phl->getId()} updated");

        return $result;
    }
}

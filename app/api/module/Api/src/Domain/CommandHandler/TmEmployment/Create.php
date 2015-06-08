<?php

/**
 * Create TmEmployment
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\TmEmployment;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Api\Entity\Tm\TmEmployment;
use Dvsa\Olcs\Api\Entity\Tm\TransportManagerApplication;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Transfer\Command\TmEmployment\Create as CreateCommand;

/**
 * Create TmEmployment
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
final class Create extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'TmEmployment';

    protected $extraRepos = ['TransportManagerApplication'];

    public function handleCommand(CommandInterface $command)
    {
        /* @var $command CreateCommand */

        $tmEmployment = new TmEmployment();
        $tmEmployment->setPosition($command->getPosition());
        $tmEmployment->setHoursPerWeek($command->getHoursPerWeek());
        $tmEmployment->setEmployerName($command->getEmployerName());
        /* @var $tma TransportManagerApplication */
        $tma = $this->getRepo('TransportManagerApplication')->fetchById($command->getTmaId());
        $tmEmployment->setTransportManager($tma->getTransportManager());

        $result = $this->createContactDetails($command);
        $tmEmployment->setContactDetails(
            $this->getRepo()->getReference(
                \Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails::class,
                $result->getId('contactDetails')
            )
        );

        $this->getRepo()->save($tmEmployment);

        $result = new Result();
        $result->addId(lcfirst($this->repoServiceName), $tmEmployment->getId());
        $result->addMessage("Tm Employment ID {$tmEmployment->getId()} created");

        return $result;
    }

    /**
     * Create Contact Details entity
     *
     * @param CreateCommand $command
     *
     * @return Result
     */
    protected function createContactDetails(CreateCommand $command)
    {
        $response = $this->getCommandHandler()->handleCommand(
            \Dvsa\Olcs\Api\Domain\Command\ContactDetails\SaveAddress::create(
                [
                    'addressLine1' => $command->getAddress()['addressLine1'],
                    'addressLine2' => $command->getAddress()['addressLine2'],
                    'addressLine3' => $command->getAddress()['addressLine3'],
                    'addressLine4' => $command->getAddress()['addressLine4'],
                    'town' => $command->getAddress()['town'],
                    'postcode' => $command->getAddress()['postcode'],
                    'countryCode' => $command->getAddress()['countryCode'],
                    'contactType' => \Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails::CONTACT_TYPE_TRANSPORT_MANAGER,
                ]
            )
        );

        return $response;
    }
}

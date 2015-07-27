<?php

/**
 * Create Unlicensed Operator
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Operator;

use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\AbstractCommandHandler;
use Dvsa\Olcs\Api\Domain\CommandHandler\TransactionedInterface;
use Dvsa\Olcs\Transfer\Command\CommandInterface;
use Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails as ContactDetailsEntity;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation as OrganisationEntity;
use Dvsa\Olcs\Api\Entity\TrafficArea\TrafficArea as TrafficAreaEntity;

/**
 * Create Unlicensed Operator
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
final class CreateUnlicensed extends AbstractCommandHandler implements TransactionedInterface
{
    protected $repoServiceName = 'Licence';

    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();

        $organisation = new OrganisationEntity();
        $organisation
            ->setName($command->getName())
            ->setIsUnlicensed(true)
            ->setType($this->getRepo()->getRefdataReference(OrganisationEntity::ORG_TYPE_OTHER));

        $contactDetails = new ContactDetailsEntity(
            $this->getRepo()->getRefdataReference(ContactDetailsEntity::CONTACT_TYPE_CORRESPONDENCE_ADDRESS)
        );
        // @TODO add address and phone data

        $licence = new LicenceEntity(
            $organisation,
            $this->getRepo()->getRefdataReference(LicenceEntity::LICENCE_STATUS_UNLICENSED)
        );
        $licence
            ->setOrganisation($organisation)
            ->setTrafficArea($this->getRepo()->getReference(TrafficAreaEntity::class, $command->getTrafficArea()))
            ->setGoodsOrPsv($this->getRepo()->getRefdataReference($command->getOperatorType()))
            ->setLicenceType($this->getRepo()->getRefdataReference(LicenceEntity::LICENCE_TYPE_RESTRICTED))
            ->setNiFlag($command->getTrafficArea() == TrafficAreaEntity::NORTHERN_IRELAND_TRAFFIC_AREA_CODE)
            ->setCorrespondenceCd($contactDetails);

        $this->getRepo()->save($licence);

        $result
            ->addId('licence', $licence->getId())
            ->addMessage('Licence added')
            ->addId('organisation', $licence->getOrganisation()->getId())
            ->addMessage('Organisation added')
            ->addId('contactDetails', $licence->getCorrespondenceCd()->getId())
            ->addMessage('ContactDetails added');

        return $result;
    }
}

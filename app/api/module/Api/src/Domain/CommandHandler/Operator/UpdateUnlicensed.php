<?php

/**
 * Update Unlicensed Operator
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
namespace Dvsa\Olcs\Api\Domain\CommandHandler\Operator;

use Doctrine\ORM\Query;
use Dvsa\Olcs\Api\Domain\Command\Result;
use Dvsa\Olcs\Api\Domain\CommandHandler\Operator\UnlicensedAbstract as AbstractCommandHandler;
use Dvsa\Olcs\Api\Entity\ContactDetails\ContactDetails as ContactDetailsEntity;
use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation as OrganisationEntity;
use Dvsa\Olcs\Api\Entity\TrafficArea\TrafficArea as TrafficAreaEntity;
use Dvsa\Olcs\Transfer\Command\CommandInterface;

/**
 * Update Unlicensed Operator
 *
 * @author Dan Eggleston <dan@stolenegg.com>
 */
final class UpdateUnlicensed extends AbstractCommandHandler
{
    protected $repoServiceName = 'Organisation';

    protected $extraRepos = ['Licence', 'ContactDetails'];

    public function handleCommand(CommandInterface $command)
    {
        $result = new Result();

        /* @var $organisation OrganisationEntity */
        $organisation = $this->getRepo()->fetchUsingId($command, Query::HYDRATE_OBJECT, $command->getVersion());

        // only ever one 'licence' for unlicensed operators
        $licence = $organisation->getLicences()->first();
        $contactDetails = $licence->getCorrespondenceCd();

        $this->updateLicence($licence, $command);
        $this->updateOrganisation($organisation, $command);
        $this->updateContactDetails($contactDetails, $command);

        // save the licence, children will cascade persist
        $this->getRepo('Licence')->save($licence);

        $result
            ->addId('organisation', $organisation->getId())
            ->addId('licence', $licence->getId())
            ->addId('contactDetails', $contactDetails->getId())
            ->addMessage('Updated');

        return $result;
    }

    /**
     * @param OrganisationEntity $organisation
     * @param CommandInterface $command
     * @return OrganisationEntity
     */
    private function updateOrganisation(OrganisationEntity $organisation, CommandInterface $command)
    {
        $organisation
            ->setName($command->getName());

        return $organisation;
    }

    /**
     * @param LicenceEntity $licence
     * @param CommandInterface $command
     * @return LicenceEntity
     */
    private function updateLicence(LicenceEntity $licence, CommandInterface $command)
    {
        $niFlag = $command->getTrafficArea() === TrafficAreaEntity::NORTHERN_IRELAND_TRAFFIC_AREA_CODE ? 'Y' : 'N';

        $licence
            ->setTrafficArea($this->getRepo()->getReference(TrafficAreaEntity::class, $command->getTrafficArea()))
            ->setGoodsOrPsv($this->getRepo()->getRefdataReference($command->getOperatorType()))
            ->setNiFlag($niFlag);

        // update licence number
        $licNoNumericPart = filter_var($licence->getLicNo(), FILTER_SANITIZE_NUMBER_INT);
        $newLicNo = $this->buildLicenceNumber(
            $licence->getCategoryPrefix(),
            $licence->getTrafficArea()->getId(),
            $licNoNumericPart
        );
        $licence->setLicNo($newLicNo);

        return $licence;
    }

    /**
     * @param ContactDetailsEntity $contactDetails
     * @param CommandInterface $command
     * @return ContactDetailsEntity
     */
    private function updateContactDetails(ContactDetailsEntity $contactDetails, CommandInterface $command)
    {
        $contactDetails->update(
            $this->getRepo('ContactDetails')->populateRefDataReference(
                $command->getContactDetails()
            )
        );

        return $contactDetails;
    }
}

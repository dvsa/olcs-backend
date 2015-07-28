<?php

namespace Dvsa\Olcs\Api\Service\Publication\Process;

use Dvsa\Olcs\Api\Entity\Publication\PublicationLink;
use Dvsa\Olcs\Api\Service\Publication\ImmutableArrayObject;

use Dvsa\Olcs\Api\Entity\Licence\Licence as LicenceEntity;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation as OrganisationEntity;
use Dvsa\Olcs\Api\Entity\Organisation\OrganisationPerson as OrganisationPersonEntity;
use Dvsa\Olcs\Api\Entity\Person\Person as PersonEntity;

class Text1 implements ProcessInterface
{
    protected $previousPublication = '(Previous Publication:(%s))';
    protected $previousHearingAdjourned = 'Previous hearing on %s was adjourned.';
    protected $tradingAs = 'T/A %s';
    protected $orgTypeLtd = 'org_t_rc';
    protected $orgTypeLlp = 'org_t_llp';
    protected $pi = '';

    /**
     * @param PublicationLink $publication
     * @param ImmutableArrayObject $context
     * @return PublicationLink
     */
    public function process(PublicationLink $publication, ImmutableArrayObject $context)
    {
        $hearingText = [];
        $hearingText[] = $this->getOpeningText($publication, $context);

        //previous publication
        if ($context->offsetExists('previousPublication')) {
            $hearingText[] = $this->getPreviousPublication($context->offsetGet('previousPublication'));
        }

        //previous hearing, only present on hearing publication, not on decision
        if ($context->offsetExists('previousHearing')) {
            $hearingText[] = $this->getPreviousHearing($context->offsetGet('previousHearing'));
        }

        //licence info
        $hearingText[] = $this->getLicenceInfo($publication->getLicence());

        //person data
        $hearingText[] = $this->getPersonInfo($publication->getLicence()->getOrganisation());

        //licence address
        if ($context->offsetExists('licenceAddress')) {
            $hearingText[] = "\n" . strtoupper($context->offsetGet('licenceAddress'));
        }

        $publication->setText1(implode(' ', $hearingText));

        return $publication;
    }

    /**
     * @param PublicationLink $publication
     * @param ImmutableArrayObject $context
     * @return String
     */
    public function getOpeningText(PublicationLink $publication, ImmutableArrayObject $context)
    {
        return sprintf(
            $this->pi,
            $publication->getPi()->getId(),
            $context->offsetGet('piVenueOther'),
            $context->offsetGet('formattedHearingDate'),
            $context->offsetGet('formattedHearingTime')
        );
    }

    /**
     * @param String $previousPublication
     * @return String
     */
    public function getPreviousPublication($previousPublication)
    {
        return sprintf($this->previousPublication, $previousPublication);
    }

    /**
     * @param LicenceEntity $licence
     * @return string
     */
    public function getLicenceInfo(LicenceEntity $licence)
    {
        $organisation = $licence->getOrganisation();
        $tradingNames = $organisation->getTradingNames();

        $licence = "\n" . sprintf(
                '%s %s '. "\n" . '%s',
                $licence->getLicNo(),
                $licence->getLicenceType()->getOlbsKey(),
                $organisation->getName()
            );

        if (!empty($tradingNames)) {
            $latestTradingName = $tradingNames->last();
            $licence .= "\n" . sprintf($this->tradingAs, $latestTradingName->getName());
        }

        return strtoupper($licence);
    }

    /**
     * @param OrganisationEntity $organisation
     * @return string
     */
    public function getPersonInfo($organisation)
    {
        $organisationPersons = $organisation->getOrganisationPersons();
        $orgType = $organisation->getType()->getId();
        $persons = [];

        switch ($orgType) {
            case OrganisationEntity::ORG_TYPE_REGISTERED_COMPANY :
                $prefix = 'Director(s): ';
                break;
            case OrganisationEntity::ORG_TYPE_LLP:
                $prefix = 'Partner(s): ';
                break;
            default:
                $prefix = '';
        }

        /**
         * @var PersonEntity $person
         * @var OrganisationPersonEntity $organisationPerson
         */
        foreach ($organisationPersons as $organisationPerson) {
            $person = $organisationPerson->getPerson();
            $persons[] = strtoupper(sprintf('%s %s', $person->getForename(), $person->getFamilyName()));
        }

        return "\n" . $prefix . implode(', ', $persons);
    }

    /**
     * @param String $previousHearing
     * @return string
     */
    public function getPreviousHearing($previousHearing)
    {
        $previousHearingDate = new \DateTime($previousHearing);
        return sprintf($this->previousHearingAdjourned, $previousHearingDate->format('d F Y'));
    }
}

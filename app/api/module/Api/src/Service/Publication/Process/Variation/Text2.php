<?php

namespace Dvsa\Olcs\Api\Service\Publication\Process\Variation;

use Dvsa\Olcs\Api\Entity\Publication\PublicationLink;
use Dvsa\Olcs\Api\Service\Publication\ImmutableArrayObject;
use Dvsa\Olcs\Api\Service\Publication\Process\ProcessInterface;
use Dvsa\Olcs\Api\Entity\Organisation\Organisation;

/**
 * Variation Text2
 *
 * @author Mat Evans <mat.evans@valtech.co.uk>
 */
final class Text2 implements ProcessInterface
{
    /**
     * @param PublicationLink $publicationLink
     * @param ImmutableArrayObject $context
     */
    public function process(PublicationLink $publicationLink, ImmutableArrayObject $context)
    {
        $text[0] = $this->getOperatorName($publicationLink);
        $oldestTradingName = $this->getOldestTradingName($publicationLink);
        if ($oldestTradingName !== null) {
            $text[0] .= ' T/A '. $oldestTradingName;
        }

        $peopleText = $this->getPeopleText($publicationLink, $context);
        if ($peopleText) {
            $text[1] = $peopleText;
        }

        $publicationLink->setText2(implode("\n", $text));
    }

    /**
     * Get Operator name
     *
     * @param PublicationLink $publication
     *
     * @return string
     */
    private function getOperatorName(PublicationLink $publication)
    {
        return $publication->getLicence()->getOrganisation()->getName();
    }

    /**
     * Get oldest trading name
     *
     * @param PublicationLink $publication
     *
     * @return string
     */
    private function getOldestTradingName(PublicationLink $publication)
    {
        $tradingNames = $publication->getLicence()->getOrganisation()->getTradingNames();

        if ($tradingNames->isEmpty()) {
            return null;
        }

        // Assume they are NOT already in the correct order, find the oldest trading name by lowset PK.
        /* @var $oldestTradingName \Dvsa\Olcs\Api\Entity\Organisation\TradingName */
        $oldestTradingName = null;
        foreach ($tradingNames as $tradingName) {
            if ($oldestTradingName === null || $tradingName->getId() < $oldestTradingName->getId()) {
                $oldestTradingName = $tradingName;
            }
        }

        return $oldestTradingName->getName();
    }

    /**
     * Get people text
     *
     * @param PublicationLink $publication
     *
     * @return string
     */
    private function getPeopleText(PublicationLink $publication, ImmutableArrayObject $context)
    {
        $organisation = $publication->getLicence()->getOrganisation();
        // if sole trader then no text
        if ($organisation->isSoleTrader()) {
            return null;
        }

        // if no data in context
        if (!$context->offsetExists('applicationPeople') || !is_array($context->offsetGet('applicationPeople'))) {
            return null;
        }

        $personPrefixes = [
            Organisation::ORG_TYPE_REGISTERED_COMPANY => 'Director(s): ',
            Organisation::ORG_TYPE_PARTNERSHIP => 'Partner(s): ',
            Organisation::ORG_TYPE_LLP => 'Partner(s): ',
            // @todo Need to find out what this should be
            Organisation::ORG_TYPE_OTHER => '',
        ];
        $text = (isset($personPrefixes[$organisation->getType()->getId()])) ?
            $personPrefixes[$organisation->getType()->getId()] :
            '';
        $people = [];
        foreach ($context->offsetGet('applicationPeople') as $person) {
            /* @var $person \Dvsa\Olcs\Api\Entity\Person\Person */
            $people[] = $person->getForename() .' '. $person->getFamilyName();
        }

        $text .= implode(', ', $people);

        return $text;
    }
}

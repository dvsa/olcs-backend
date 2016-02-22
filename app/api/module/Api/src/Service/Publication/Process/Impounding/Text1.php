<?php

namespace Dvsa\Olcs\Api\Service\Publication\Process\Impounding;

use Dvsa\Olcs\Api\Entity\Organisation\Organisation;
use Dvsa\Olcs\Api\Entity\Publication\PublicationLink;
use Dvsa\Olcs\Api\Service\Publication\ImmutableArrayObject;
use \Dvsa\Olcs\Api\Service\Publication\Process\AbstractText;

/**
 * Class Impounding Text1
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
final class Text1 extends AbstractText
{
    protected $impoundingDetailsText = 'Impounding hearing (%s) to be held at %s, on %s commencing at %s';

    /**
     * @param PublicationLink $publication
     * @param ImmutableArrayObject $context
     * @return PublicationLink
     */
    public function process(PublicationLink $publicationLink, ImmutableArrayObject $context)
    {
        $this->addTextLine(
            $this->getImpoundingDetailsText($publicationLink, $context)
        );
        $this->addTextLine($context->offsetGet('licenceNo'));
        $this->addTextLine($context->offsetGet('tradingAs'));


        //$this->addTextLine($context->offsetGet('licenceOrganisationDetails'));
        $this->getOrganisationOfficersText($publicationLink, $context);

        $this->addTextLine($context->offsetGet('licenceAddress'));

        $publicationLink->setText1($this->getTextWithNewLine());

        return $publicationLink;
    }

    /**
     * @param PublicationLink $publication
     * @param ImmutableArrayObject $context
     * @return String
     */
    private function getImpoundingDetailsText(PublicationLink $publicationLink, ImmutableArrayObject $context)
    {
        return sprintf(
            $this->impoundingDetailsText,
            $publicationLink->getImpounding()->getId(),
            $context->offsetGet('piVenueOther'),
            $context->offsetGet('formattedHearingDate'),
            $context->offsetGet('formattedHearingTime')
        );
    }


    /**
     * @param PublicationLink $publication
     * @param ImmutableArrayObject $context
     * @return String
     */
    private function getOrganisationOfficersText(PublicationLink $publicationLink, ImmutableArrayObject $context)
    {
        $organisationType = $context->offsetGet('organisationType');
        $prefix = '';
        if ($organisationType['id'] == Organisation::ORG_TYPE_PARTNERSHIP ||
            $organisationType['id'] == Organisation::ORG_TYPE_LLP
        ) {
            $prefix = 'Partner(s)';
        }

        if ($organisationType['id'] == Organisation::ORG_TYPE_REGISTERED_COMPANY) {
            $prefix = 'Director(s)';
        }

        $licencePeople = $context->offsetGet('licencePeople');

        if (!empty($licencePeople)) {
            for ($i = 0; $i < count($licencePeople); $i++) {
                if ($i == 0) {
                    $this->addTextLine($prefix . ' ' . $licencePeople->getFullName());
                }
                $this->addTextLine($licencePeople->getFullName());
            }
        }
    }



}

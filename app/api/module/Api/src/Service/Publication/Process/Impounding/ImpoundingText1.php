<?php

namespace Dvsa\Olcs\Api\Service\Publication\Process\Impounding;

use Dvsa\Olcs\Api\Entity\Publication\PublicationLink;
use Dvsa\Olcs\Api\Service\Publication\ImmutableArrayObject;
use \Dvsa\Olcs\Api\Service\Publication\Process\AbstractText;

/**
 * Class Impounding Text1
 *
 * @author Shaun Lizzio <shaun@lizzio.co.uk>
 */
final class ImpoundingText1 extends AbstractText
{
    protected $impoundingHearingText = 'Impounding hearing (%s) to be held at $s, on %s commencing at %s';

    /**
     * @param PublicationLink $publication
     * @param ImmutableArrayObject $context
     * @return PublicationLink
     */
    public function process(PublicationLink $publicationLink, ImmutableArrayObject $context)
    {
        $this->addTextLine(
            $this->getOpeningText($publicationLink, $context)
        );

        $publicationLink->setText1($this->getTextWithNewLine());

        return $publicationLink;
    }

    /**
     * @param PublicationLink $publication
     * @param ImmutableArrayObject $context
     * @return String
     */
    private function getOpeningText(PublicationLink $publication, ImmutableArrayObject $context)
    {
        return sprintf(
            $this->impoundingHearingText,
            $publication->getImpounding()->getId(),
            $context->offsetGet('piVenue'),
            $context->offsetGet('piVenueOther'),
            $context->offsetGet('formattedHearingDate'),
            $context->offsetGet('formattedHearingTime')
        );
    }
}

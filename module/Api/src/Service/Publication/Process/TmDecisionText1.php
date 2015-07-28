<?php

/**
 * Tm Decision text 1
 */
namespace Dvsa\Olcs\Api\Service\Publication\Process;

use Dvsa\Olcs\Api\Entity\Publication\PublicationLink;
use Dvsa\Olcs\Api\Service\Publication\ImmutableArrayObject;

/**
 * Tm Decision text 1
 */
class TmDecisionText1 extends Text1
{
    protected $pi = 'TM Public Inquiry (Case ID: %s, Public Inquiry ID: %s) for %s held at %s,
    on %s at %s';

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
            $publication->getPi()->getCase()->getId(),
            $context->offsetGet('id'),
            $context->offsetGet('transportManagerName'),
            $context->offsetGet('piVenueOther'),
            $context->offsetGet('formattedHearingDate'),
            $context->offsetGet('formattedHearingTime')
        );
    }
}

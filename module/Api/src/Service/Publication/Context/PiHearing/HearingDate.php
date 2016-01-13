<?php

namespace Dvsa\Olcs\Api\Service\Publication\Context\PiHearing;

use Dvsa\Olcs\Api\Service\Publication\Context\AbstractContext;
use Dvsa\Olcs\Api\Entity\Publication\PublicationLink;

/**
 * Class HearingDate
 * @package Dvsa\Olcs\Api\Service\Publication\Context\PiHearing
 * @author Ian Lindsay <ian@hemera-business-services.co.uk>
 */
final class HearingDate extends AbstractContext
{
    public function provide(PublicationLink $publication, \ArrayObject $context)
    {
        $hearingDateTime = new \DateTime($context->offsetGet('hearingDate'));

        $context->offsetSet('formattedHearingDate', $hearingDateTime->format('j F Y'));
        $context->offsetSet('formattedHearingTime', $hearingDateTime->format('H:i'));

        return $context;
    }
}

<?php

namespace Dvsa\Olcs\Api\Service\Publication\Context\Variation;

use ArrayObject;
use Dvsa\Olcs\Api\Entity\Publication\PublicationLink;
use Dvsa\Olcs\Api\Service\Publication\Context\AbstractContext;

/**
 * Authorisations
 *
 * @author Jonathan Thomas <jonathan@opalise.co.uk>
 */
final class Authorisations extends AbstractContext
{
    /**
     * @param PublicationLink $publicationLink
     * @param ArrayObject $context
     *
     * @return ArrayObject
     */
    public function provide(PublicationLink $publicationLink, ArrayObject $context)
    {
        $variation = $publicationLink->getApplication();

        if ($variation->hasLgvAuthorisationIncreased()) {
            $text = sprintf(
                'Light goods vehicles authorised on the licence. New authorisation will be %d vehicle(s)',
                $variation->getTotAuthLgvVehicles()
            );

            $context->offsetSet('authorisation', $text);
        }

        return $context;
    }
}

<?php

namespace Dvsa\Olcs\Api\Service\Publication\Context\PiHearing;

use Dvsa\Olcs\Api\Domain\Query\Bookmark\PiVenueBundle;
use Dvsa\Olcs\Api\Service\Publication\Context\AbstractContext;
use Dvsa\Olcs\Api\Entity\Publication\PublicationLink;
use Dvsa\Olcs\Api\Entity\Pi\PiVenue as PiVenueEntity;
use Dvsa\Olcs\Api\Service\Helper\FormatAddress;


class PiVenue extends AbstractContext
{
    private static $bundle = [];

    public function provide(PublicationLink $publication, \ArrayObject $context)
    {
        if ($context->offsetGet('piVenue')) {
            $query = PiVenueBundle::create(['id' => $context->offsetGet('piVenue'), 'bundle' => self::$bundle]);

            /**
             * @var PiVenueEntity $piVenue
             */
            $piVenue = $this->handleQuery($query);
            $address = new FormatAddress();

            $venueDetails = $piVenue->getName() . ', ' . $address->format($piVenue->getAddress());

            $context->offsetSet('piVenueOther', $venueDetails);
        }

        return $context;
    }
}
